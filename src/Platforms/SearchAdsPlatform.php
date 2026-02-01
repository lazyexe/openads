<?php

namespace OpenAds\Platforms;

use OpenAds\Contracts\PlatformInterface;
use OpenAds\Core\AdContext;
use OpenAds\Core\AdCollection;
use OpenAds\Core\Pipeline;
use OpenAds\Matching\KeywordMatcher;
use OpenAds\Scoring\QualityScore;
use OpenAds\Auction\DefaultAuction;

class SearchAdsPlatform implements PlatformInterface
{
    public function run(AdContext $context): AdCollection
    {
        return (new Pipeline(
            new KeywordMatcher(),
            new QualityScore(),
            new DefaultAuction()
        ))->process($context);
    }
}
