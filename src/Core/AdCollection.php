<?php

namespace OpenAds\Core;

class AdCollection implements \Countable, \IteratorAggregate
{
    protected array $ads;

    public function __construct(array $ads = [])
    {
        $this->ads = $ads;
    }

    public function all(): array
    {
        return $this->ads;
    }

    public function limit(int $total): self
    {
        $limited = array_slice($this->ads, 0, $total);
        return new self($limited);
    }

    public function count(): int
    {
        return count($this->ads);
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->ads);
    }
}
