<?php

namespace OpenAds\Matching;

use OpenAds\Contracts\MatcherInterface;
use OpenAds\Core\AdContext;
use OpenAds\DTO\AdDTO;
use OpenAds\DTO\AdAssetDTO;
use Illuminate\Support\Facades\DB;

class KeywordMatcher implements MatcherInterface
{
    public function match(AdContext $context): array
    {
        $query = strtolower($context->query);

        $ads = DB::table('ads_ads')
            ->join('ads_ad_groups', 'ads_ads.ad_group_id', '=', 'ads_ad_groups.id')
            ->join('ads_campaigns', 'ads_ad_groups.campaign_id', '=', 'ads_campaigns.id')
            ->leftJoin('ads_keywords', 'ads_keywords.ad_group_id', '=', 'ads_ad_groups.id')
            ->where('ads_ads.status', 'active')
            ->where('ads_ad_groups.status', 'active')
            ->where('ads_campaigns.status', 'active')
            ->whereRaw('ads_campaigns.daily_budget > 0')
            ->where(function($q){
                $q->whereNull('ads_campaigns.start_date')
                  ->orWhere('ads_campaigns.start_date', '<=', now());
            })
            ->where(function($q){
                $q->whereNull('ads_campaigns.end_date')
                  ->orWhere('ads_campaigns.end_date', '>=', now());
            })
            ->where(function($q) use ($context) {
                $currentTime = now()->format('H:i:s');

                $q->where(function($sub) use ($currentTime) {
                    $sub->whereNull('ads_campaigns.start_time')
                        ->orWhere('ads_campaigns.start_time', '<=', $currentTime);
                })->where(function($sub) use ($currentTime) {
                    $sub->whereNull('ads_campaigns.end_time')
                        ->orWhere('ads_campaigns.end_time', '>=', $currentTime);
                });

                if (!empty($context->meta['country']) || !empty($context->meta['city'])) {
                    $country = $context->meta['country'] ?? '';
                    $city = $context->meta['city'] ?? '';
                    $q->where(function($sub) use ($country, $city) {
                        $sub->whereNull('ads_campaigns.target_locations')
                            ->orWhereJsonContains('ads_campaigns.target_locations', $country)
                            ->orWhereJsonContains('ads_campaigns.target_locations', $city);
                    });
                }

                if (!empty($context->meta['device'])) {
                    $device = $context->meta['device'];
                    $q->where(function($sub) use ($device) {
                        $sub->whereNull('ads_campaigns.target_devices')
                            ->orWhereJsonContains('ads_campaigns.target_devices', $device);
                    });
                }
            })
            ->where(function($q) use ($query) {
                $q->where('ads_keywords.negative', false)
                  ->where('ads_keywords.keyword', 'like', "%{$query}%");
            })
            ->select(
                'ads_ads.*',
                'ads_ad_groups.campaign_id',
                'ads_campaigns.daily_budget as campaign_balance'
            )
            ->distinct()
            ->get();

        $result = [];

        foreach ($ads as $ad) {
            $assets = DB::table('ads_assets')
                ->where('ad_id', $ad->id)
                ->orderByDesc('is_primary')
                ->get()
                ->map(fn($asset) => new AdAssetDTO(
                    type: $asset->type,
                    source: $asset->source,
                    primary: (bool) $asset->is_primary
                ))
                ->toArray();

            $text = strtolower($ad->title . ' ' . ($ad->description ?? ''));
            similar_text($text, $query, $percent);
            $relevance = $percent / 100;

            $impressions = DB::table('ads_impressions')->where('ad_id', $ad->id)->count();
            $clicks = DB::table('ads_clicks')->where('ad_id', $ad->id)->count();
            $ctr = $impressions > 0 ? $clicks / $impressions : 0.0;
            $landingScore = $ctr;

            $dto = new AdDTO(
                id: $ad->id,
                title: $ad->title,
                description: $ad->description ?? '',
                url: $ad->url,
                bid: (float) $ad->bid,
                assets: $assets,
                ctr: $ctr,
                relevance: $relevance,
                landingScore: $landingScore,
                campaign_id: $ad->campaign_id,
                campaign_balance: (float) $ad->campaign_balance
            );

            $dto->score = $dto->bid * (
                ($dto->ctr * 0.5) + 
                ($dto->relevance * 0.3) + 
                ($dto->landingScore * 0.2)
            );

            DB::table('ads_ads')
                ->where('id', $dto->id)
                ->update([
                    'ctr' => $dto->ctr,
                    'relevance' => $dto->relevance,
                    'landing_score' => $dto->landingScore
                ]);

            $result[$dto->id] = $dto;
        }

        usort($result, fn($a, $b) => $b->score <=> $a->score);

        return array_values($result);
    }
}
