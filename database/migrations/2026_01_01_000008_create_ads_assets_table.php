<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads_assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ad_id')
                ->constrained('ads_ads')
                ->cascadeOnDelete();

            $table->enum('type', ['image', 'video', 'banner'])->default('image');
            $table->string('source');
            $table->boolean('is_primary')->default(false);

            $table->timestamps();

            $table->index(['ad_id', 'is_primary']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_assets');
    }
};
