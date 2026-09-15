<?php

declare(strict_types=1);

namespace App\Repositories\Category\Contracts;

use App\Models\Category;

interface CategoryRepositoryInterface
{
    public function findCategoryBySlug(string $categorySlug): ?Category;

    /** @return list<Category> */
    public function findCategoriesWithArticles(): array;
}
