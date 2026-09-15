<?php

declare(strict_types=1);

namespace App\DTO\Pagination;

final readonly class PaginationDTO
{
    /**
     * @param positive-int $currentPage
     * @param positive-int $totalPages
     * @param positive-int $limit
     * @param non-negative-int $offset
     */
    public function __construct(
        public int $currentPage,
        public int $totalPages,
        public int $limit,
        public int $offset,
    ) {}
}
