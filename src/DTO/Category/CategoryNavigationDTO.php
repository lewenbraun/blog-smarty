<?php

declare(strict_types=1);

namespace App\DTO\Category;

final readonly class CategoryNavigationDTO
{
    /** @param list<array{number: int, url: string}> $pageLinks */
    public function __construct(
        public string $activeSort,
        public string $viewsSortUrl,
        public string $dateSortUrl,
        public array $pageLinks,
        public ?string $previousPageUrl,
        public ?string $nextPageUrl,
    ) {}
}
