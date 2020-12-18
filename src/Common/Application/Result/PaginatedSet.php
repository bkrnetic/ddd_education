<?php

declare(strict_types=1);

namespace Common\Application\Result;

final class PaginatedSet extends Set
{
    private int $limit;

    private int $offset;

    public function __construct(int $limit, int $offset, int $totalResults, array $results)
    {
        parent::__construct($totalResults, $results);

        $this->limit = $limit;
        $this->offset = $offset;
    }

    public function getLimit(): int
    {
        return $this->limit;
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    public function getPage(): int
    {
        return (int) floor($this->offset / $this->limit) + 1;
    }

    public function getPerPage(): int
    {
        return $this->limit;
    }

    public function getTotalPages(): int
    {
        return (int) floor($this->totalResults / $this->limit) + 1;
    }
}
