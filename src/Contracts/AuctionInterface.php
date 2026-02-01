<?php

namespace OpenAds\Contracts;

interface AuctionInterface
{
    public function run(array $ads): array;
}
