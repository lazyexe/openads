<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        DB::table('ads_campaigns')->insert([
            'name' => 'Demo Campaign',
            'daily_budget' => 100000,
            'status' => 'active',
            'start_date' => $now,
            'end_date' => $now->copy()->addDays(7),
            'start_time' => '00:00:00',
            'end_time' => '23:59:59',
            'target_locations' => json_encode([
                'countries' => ['ID'],
                'cities' => ['Jakarta']
            ]),
            'target_devices' => json_encode(['android', 'ios', 'desktop']),
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $campaignId = DB::getPdo()->lastInsertId();

        DB::table('ads_ad_groups')->insert([
            'campaign_id' => $campaignId,
            'name' => 'Demo Ad Group',
            'default_bid' => 500,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adGroupId = DB::getPdo()->lastInsertId();

        DB::table('ads_ads')->insert([
            'ad_group_id' => $adGroupId,
            'title' => 'Demo Sepatu',
            'description' => 'Diskon 50% untuk sepatu lari',
            'url' => 'https://example.com/demo-sepatu',
            'bid' => 500,
            'status' => 'active',
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        $adId = DB::getPdo()->lastInsertId();

        DB::table('ads_keywords')->insert([
            'ad_group_id' => $adGroupId,
            'keyword' => 'sepatu lari',
            'match_type' => 'broad',
            'negative' => false,
            'created_at' => $now,
            'updated_at' => $now,
        ]);

        DB::table('ads_assets')->insert([
            'ad_id' => $adId,
            'type' => 'image',
            'source' => 'https://via.placeholder.com/300x250?text=Demo+Ad',
            'is_primary' => true,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        DB::table('ads_assets')->truncate();
        DB::table('ads_keywords')->truncate();
        DB::table('ads_ads')->truncate();
        DB::table('ads_ad_groups')->truncate();
        DB::table('ads_campaigns')->truncate();
    }
};
