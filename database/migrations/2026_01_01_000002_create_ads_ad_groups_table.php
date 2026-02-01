<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads_ad_groups', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')
                ->constrained('ads_campaigns')
                ->cascadeOnDelete();

            $table->string('name');
            $table->decimal('default_bid', 12, 2)->default(0);
            $table->enum('status', ['active', 'paused'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_ad_groups');
    }
};
