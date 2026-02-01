<?php

namespace OpenAds\Contracts;

use OpenAds\Core\AdContext;

interface MatcherInterface
{
    public function match(AdContext $context): array;
}
