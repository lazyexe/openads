<?php

namespace OpenAds\Facades;

use Illuminate\Support\Facades\Facade;

class Ads extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'openads';
    }
}
