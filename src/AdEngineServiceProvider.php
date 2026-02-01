<?php

namespace OpenAds;

use Illuminate\Support\ServiceProvider;
use OpenAds\Core\AdEngine;

class AdEngineServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/ads.php',
            'ads'
        );

		$this->app->singleton('openads', function () {
			return new AdEngine();
		});
    }

    public function boot(): void
    {
        $this->publishes([
            __DIR__ . '/../config/ads.php' => config_path('ads.php'),
        ], 'ads-config');

        $this->publishes([
            __DIR__ . '/../database/migrations' => database_path('migrations'),
        ], 'ads-migrations');
    }
}
