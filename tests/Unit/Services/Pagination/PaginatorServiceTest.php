<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Pagination;

use App\Services\Pagination\PaginatorService;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

final class PaginatorServiceTest extends TestCase
{
    public function testItKeepsOnePageForEmptyResults(): void
    {
        $paginationDTO = new PaginatorService()->getPagination(0, 6, -1);

        self::assertSame(1, $paginationDTO->currentPage);
        self::assertSame(1, $paginationDTO->totalPages);
        self::assertSame(6, $paginationDTO->limit);
        self::assertSame(0, $paginationDTO->offset);
    }

    public function testItClampsToLastPage(): void
    {
        $paginationDTO = new PaginatorService()->getPagination(17, 6, 100);

        self::assertSame(3, $paginationDTO->currentPage);
        self::assertSame(3, $paginationDTO->totalPages);
        self::assertSame(12, $paginationDTO->offset);
    }

    public function testItRejectsZeroPageSize(): void
    {
        $this->expectException(InvalidArgumentException::class);

        new PaginatorService()->getPagination(17, 0, 1);
    }
}
