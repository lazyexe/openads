<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads_ads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_group_id')
                ->constrained('ads_ad_groups')
                ->cascadeOnDelete();

            $table->string('title');
            $table->string('description')->nullable();
            $table->string('url');

            $table->decimal('bid', 12, 2);
            $table->float('ctr')->default(0.1);
            $table->float('relevance')->default(0.8);
            $table->float('landing_score')->default(0.8);

            $table->enum('status', ['active', 'paused'])->default('active');
            $table->timestamps();

            $table->index(['status', 'bid']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_ads');
    }
};
