<?php

declare(strict_types=1);

namespace App\Repositories\Article\Contracts;

use App\Models\Article;

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
}
