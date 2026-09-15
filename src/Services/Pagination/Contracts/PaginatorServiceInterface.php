<?php

declare(strict_types=1);

namespace App\Services\Pagination\Contracts;

use App\DTO\Pagination\PaginationDTO;

interface PaginatorServiceInterface
{
    public function getPagination(int $totalItems, int $perPage, int $page): PaginationDTO;
}
