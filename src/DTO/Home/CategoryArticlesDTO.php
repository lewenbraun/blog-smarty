<?php

declare(strict_types=1);

namespace App\DTO\Home;

use App\Models\Article;
use App\Models\Category;

final readonly class CategoryArticlesDTO
{
    /** @param list<Article> $articles */
    public function __construct(
        public Category $category,
        public array $articles,
    ) {}
}
