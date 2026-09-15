<?php

declare(strict_types=1);

namespace App\Services\Pagination;

use App\DTO\Pagination\PaginationDTO;
use App\Services\Pagination\Contracts\PaginatorServiceInterface;
use InvalidArgumentException;

final readonly class PaginatorService implements PaginatorServiceInterface
{
    public function getPagination(int $totalItems, int $perPage, int $page): PaginationDTO
    {
        if ($totalItems < 0 || $perPage < 1) {
            throw new InvalidArgumentException('Total items must be non-negative and items per page must be positive.');
        }

        $totalPages = max(1, (int) ceil($totalItems / $perPage));
        $currentPage = max(1, min($page, $totalPages));
        $offset = ($currentPage - 1) * $perPage;

        return new PaginationDTO(
            currentPage: $currentPage,
            totalPages: $totalPages,
            limit: $perPage,
            offset: $offset,
        );
    }
}
