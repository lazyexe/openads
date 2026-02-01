<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads_keywords', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_group_id')
                ->constrained('ads_ad_groups')
                ->cascadeOnDelete();

            $table->string('keyword');
            $table->enum('match_type', ['exact', 'phrase', 'broad'])
                ->default('broad');

            $table->boolean('negative')->default(false);
            $table->timestamps();

            $table->index('keyword');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_keywords');
    }
};
