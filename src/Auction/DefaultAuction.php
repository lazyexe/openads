<?php

namespace OpenAds\Auction;

use OpenAds\Contracts\AuctionInterface;

class DefaultAuction implements AuctionInterface
{
    public function run(array $ads): array
    {
        usort($ads, fn($a, $b) => $b->score <=> $a->score);
        return $ads;
    }
}
