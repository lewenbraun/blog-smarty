<?php

declare(strict_types=1);

namespace App\Services\Article;

use App\DTO\Article\ArticlePageDTO;
use App\Exceptions\ArticleNotFoundException;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Services\Article\Contracts\ArticleServiceInterface;

final readonly class ArticleService implements ArticleServiceInterface
{
    private const int SIMILAR_ARTICLES_LIMIT = 3;

    public function __construct(
        private ArticleRepositoryInterface $articleRepository,
    ) {}

    public function getArticlePage(string $articleSlug): ArticlePageDTO
    {
        $article = $this->articleRepository->findArticleBySlug($articleSlug);

        if ($article === null) {
            throw new ArticleNotFoundException($articleSlug);
        }

        $this->articleRepository->incrementArticleViews($article);

        $similarArticles = $this->articleRepository->findSimilarArticles(
            $article,
            self::SIMILAR_ARTICLES_LIMIT,
        );

        return new ArticlePageDTO(
            article: $article,
            similarArticles: $similarArticles,
        );
    }
}
