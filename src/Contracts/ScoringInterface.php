<?php

namespace OpenAds\Contracts;

interface ScoringInterface
{
    public function score(array $ads): array;
}
