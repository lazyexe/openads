<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ads_clicks', function (Blueprint $table) {
            $table->id();

            $table->foreignId('ad_id')
                ->constrained('ads_ads')
                ->cascadeOnDelete();

            $table->string('ip')->nullable();
            $table->string('user_agent')->nullable();
            $table->string('context')->nullable(); // search / display / video

            $table->timestamps();

            $table->index(['ad_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ads_clicks');
    }
};
