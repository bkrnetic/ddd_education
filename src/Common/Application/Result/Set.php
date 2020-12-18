<?php

declare(strict_types=1);

namespace Common\Application\Result;

class Set
{
    protected int $totalResults;

    /** @var mixed[] */
    private array $results;

    /**
     * @param mixed[] $results
     */
    public function __construct(int $totalResults, array $results)
    {
        $this->totalResults = $totalResults;
        $this->results = $results;
    }

    public function getTotalResults(): int
    {
        return $this->totalResults;
    }

    /**
     * @return mixed[]
     */
    public function getResults(): array
    {
        return $this->results;
    }
}
