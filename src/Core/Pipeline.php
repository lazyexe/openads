<?php

namespace OpenAds\Core;

use OpenAds\Contracts\MatcherInterface;
use OpenAds\Contracts\ScoringInterface;
use OpenAds\Contracts\AuctionInterface;

class Pipeline
{
    public function __construct(
        protected MatcherInterface $matcher,
        protected ScoringInterface $scoring,
        protected AuctionInterface $auction
    ) {}

    public function process(AdContext $context): AdCollection
    {
        $ads = $this->matcher->match($context);
        $ads = $this->scoring->score($ads);
        $ads = $this->auction->run($ads);

        return new AdCollection($ads);
    }
}
