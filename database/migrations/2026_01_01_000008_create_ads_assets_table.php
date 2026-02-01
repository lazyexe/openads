<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('ads_ads', function (Blueprint $table) {
            $table->decimal('cpc', 12, 2)->default(0)->after('bid');
            $table->decimal('cpm', 12, 2)->default(0)->after('cpc');
        });
    }

    public function down(): void
    {
        Schema::table('ads_ads', function (Blueprint $table) {
            $table->dropColumn(['cpc', 'cpm']);
        });
    }
};
