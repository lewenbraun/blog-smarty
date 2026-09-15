<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Article;

use App\Exceptions\ArticleNotFoundException;
use App\Models\Article;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Services\Article\ArticleService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class ArticleServiceTest extends TestCase
{
    public function testItBuildsArticlePage(): void
    {
        $article = $this->createArticle('main-article');
        $similarArticle = $this->createArticle('similar-article');
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);

        $articleRepository
            ->expects($this->once())
            ->method('findArticleBySlug')
            ->with('main-article')
            ->willReturn($article);
        $articleRepository
            ->expects($this->once())
            ->method('incrementArticleViews')
            ->with($article);
        $articleRepository
            ->expects($this->once())
            ->method('findSimilarArticles')
            ->with($article, 3)
            ->willReturn([$similarArticle]);

        $articlePageDTO = new ArticleService($articleRepository)
            ->getArticlePage('main-article');

        self::assertSame($article, $articlePageDTO->article);
        self::assertSame([$similarArticle], $articlePageDTO->similarArticles);
    }

    public function testItThrowsWhenArticleDoesNotExist(): void
    {
        $articleRepository = self::createStub(ArticleRepositoryInterface::class);
        $articleRepository
            ->method('findArticleBySlug')
            ->willReturn(null);

        $articleService = new ArticleService($articleRepository);

        $this->expectException(ArticleNotFoundException::class);

        $articleService->getArticlePage('missing-article');
    }

    private function createArticle(string $articleSlug): Article
    {
        return new Article(
            imageUrl: 'https://example.com/article.webp',
            title: 'Test article',
            slug: $articleSlug,
            description: 'Test description',
            content: 'Test content',
            publishedAt: new DateTimeImmutable('2026-09-14 10:00:00'),
        );
    }
}
