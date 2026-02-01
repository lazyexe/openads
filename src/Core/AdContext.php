<?php

namespace OpenAds\Core;

class AdContext
{
    public function __construct(
        public string $query,
        public array $meta = []
    ) {}
}
