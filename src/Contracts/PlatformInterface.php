<?php

namespace OpenAds\Contracts;

use OpenAds\Core\AdContext;
use OpenAds\Core\AdCollection;

interface PlatformInterface
{
    public function run(AdContext $context): AdCollection;
}
