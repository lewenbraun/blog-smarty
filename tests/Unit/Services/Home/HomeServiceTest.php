<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Home;

use App\DTO\Home\CategoryArticlesDTO;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use App\Services\Home\HomeService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class HomeServiceTest extends TestCase
{
    public function testItBuildsHomePageWithArticlesForEachCategory(): void
    {
        $businessCategory = new Category('Business', 'business', 'Business articles.');
        $technologyCategory = new Category('Technology', 'technology', 'Technology articles.');
        $sharedArticle = $this->createArticle('shared-article');
        $technologyArticle = $this->createArticle('technology-article');
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);

        $categoryRepository
            ->expects($this->once())
            ->method('findCategoriesWithArticles')
            ->willReturn([$businessCategory, $technologyCategory]);
        $articleRepository
            ->expects($this->exactly(2))
            ->method('findLatestArticlesByCategory')
            ->willReturnMap([
                [$businessCategory, 3, [$sharedArticle]],
                [$technologyCategory, 3, [$technologyArticle, $sharedArticle]],
            ]);
        $articleRepository
            ->expects($this->never())
            ->method('incrementArticleViews');

        $homeService = new HomeService($categoryRepository, $articleRepository);
        $categoryArticlesDTOs = $homeService->getHomePage();

        self::assertSame(
            [$businessCategory, $technologyCategory],
            array_map(
                static fn(CategoryArticlesDTO $categoryArticlesDTO): Category => $categoryArticlesDTO->category,
                $categoryArticlesDTOs,
            ),
        );
        self::assertSame(
            [[$sharedArticle], [$technologyArticle, $sharedArticle]],
            array_map(
                static fn(CategoryArticlesDTO $categoryArticlesDTO): array => $categoryArticlesDTO->articles,
                $categoryArticlesDTOs,
            ),
        );
    }

    public function testItReturnsEmptyHomePageWhenThereAreNoCategoriesWithArticles(): void
    {
        $categoryRepository = $this->createMock(CategoryRepositoryInterface::class);
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);

        $categoryRepository
            ->expects($this->once())
            ->method('findCategoriesWithArticles')
            ->willReturn([]);
        $articleRepository
            ->expects($this->never())
            ->method('findLatestArticlesByCategory');

        $homeService = new HomeService($categoryRepository, $articleRepository);

        self::assertSame([], $homeService->getHomePage());
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
