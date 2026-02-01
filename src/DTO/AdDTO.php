<?php

namespace OpenAds\DTO;

class AdDTO
{
    public function __construct(
        public int $id,
        public string $title,
        public string $description = '',
        public string $url,
        public float $bid,
        public array $assets = [],
        public float $ctr = 0.0,
        public float $relevance = 0.0,
        public float $landingScore = 0.0,
        public float $score = 0.0,
        public int $campaign_id = 0,
        public float $campaign_balance = 0
    ) {}
}
