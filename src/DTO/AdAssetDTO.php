<?php

namespace OpenAds\DTO;

class AdAssetDTO
{
    public function __construct(
        public string $type,
        public string $source,
        public bool $primary = false
    ) {}
}
