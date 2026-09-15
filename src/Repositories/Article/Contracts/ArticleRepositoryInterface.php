<?php

declare(strict_types=1);

namespace App\Repositories\Article\Contracts;

use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;
use App\Models\Article;
use App\Models\Category;

interface ArticleRepositoryInterface
{
    public function findArticleBySlug(string $articleSlug): ?Article;

    public function incrementArticleViews(Article $article): void;

    /**
     * @param positive-int $limit
     *
     * @return list<Article>
     */
    public function findSimilarArticles(Article $article, int $limit): array;

    /**
     * @param positive-int $limit
     *
     * @return list<Article>
     */
    public function findLatestArticlesByCategory(Category $category, int $limit): array;

    public function countArticlesByCategory(Category $category): int;

    /**
     * @param positive-int $limit
     * @param non-negative-int $offset
     *
     * @return list<Article>
     */
    public function findArticlesByCategory(
        Category $category,
        ArticleSortEnum $sort,
        SortDirectionEnum $direction,
        int $limit,
        int $offset,
    ): array;
}
