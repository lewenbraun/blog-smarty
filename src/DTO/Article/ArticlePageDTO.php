<?php

declare(strict_types=1);

namespace App\DTO\Article;

use App\Models\Article;

final readonly class ArticlePageDTO
{
    /** @param list<Article> $similarArticles */
    public function __construct(
        public Article $article,
        public array $similarArticles,
    ) {}
}
