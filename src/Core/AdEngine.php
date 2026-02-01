<?php

namespace OpenAds\Core;

use OpenAds\Platforms\SearchAdsPlatform;
use Illuminate\Support\Facades\DB;

class AdEngine
{
    public function search(string $query): AdCollection
    {
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        if (preg_match('/android/i', $userAgent)) {
            $device = 'android';
        } elseif (preg_match('/iphone|ipad|ipod/i', $userAgent)) {
            $device = 'ios';
        } else {
            $device = 'desktop';
        }

        $country = $this->detectCountry($ip);
        $city = $this->detectCity($ip);

        $meta = [
            'device' => $device,
            'country' => $country,
            'city' => $city,
        ];

        $collection = (new SearchAdsPlatform())
            ->run(new AdContext($query, $meta));

        foreach ($collection->all() as $ad) {
            DB::table('ads_impressions')->insert([
                'ad_id' => $ad->id,
                'ip' => $ip,
                'user_agent' => $userAgent,
                'context' => json_encode([
                    'query' => $query,
                    'page' => request()->path(),
                    'platform' => 'search',
                ]),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $percent = config('ads.view_cost_percent', 0.2);
            $cost = $ad->bid * $percent;

            if ($ad->campaign_balance >= $cost) {
                DB::table('ads_campaigns')
                    ->where('id', $ad->campaign_id)
                    ->decrement('daily_budget', $cost);
            }
        }

        return $collection;
    }

    public function logClick(int $adId): ?string
    {
        $row = DB::table('ads_ads')
            ->join('ads_ad_groups', 'ads_ads.ad_group_id', '=', 'ads_ad_groups.id')
            ->join('ads_campaigns', 'ads_ad_groups.campaign_id', '=', 'ads_campaigns.id')
            ->where('ads_ads.id', $adId)
            ->select(
                'ads_ads.id as ad_id',
                'ads_ads.url',
                'ads_ads.bid',
                'ads_campaigns.id as campaign_id',
                'ads_campaigns.daily_budget'
            )
            ->first();

        if (!$row) return null;

        $cost = $row->bid;

        if ($row->daily_budget >= $cost) {
            DB::table('ads_campaigns')
                ->where('id', $row->campaign_id)
                ->decrement('daily_budget', $cost);

            DB::table('ads_clicks')->insert([
                'ad_id' => $row->ad_id,
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'context' => 'click',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        return $row->url;
    }

    protected function detectCountry(string $ip): ?string
    {
        return 'ID';
    }
	
    protected function detectCity(string $ip): ?string
    {
        return 'Jakarta';
    }
}
