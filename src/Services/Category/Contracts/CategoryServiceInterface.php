<?php

declare(strict_types=1);

namespace App\Services\Category\Contracts;

use App\DTO\Category\CategoryPageDTO;
use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;

interface CategoryServiceInterface
{
    public function getCategoryPage(
        string $categorySlug,
        ?ArticleSortEnum $sort = null,
        SortDirectionEnum $direction = SortDirectionEnum::Desc,
        int $page = 1,
    ): CategoryPageDTO;
}
