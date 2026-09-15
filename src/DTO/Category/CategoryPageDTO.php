<?php

declare(strict_types=1);

namespace App\DTO\Category;

use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;
use App\Models\Article;
use App\Models\Category;

final readonly class CategoryPageDTO
{
    /** @param list<Article> $articles */
    public function __construct(
        public Category $category,
        public array $articles,
        public ?ArticleSortEnum $sort,
        public SortDirectionEnum $direction,
        public int $currentPage,
        public int $totalPages,
        public CategoryNavigationDTO $categoryNavigationDTO,
    ) {}
}
