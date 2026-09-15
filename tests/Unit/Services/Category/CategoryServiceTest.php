<?php

declare(strict_types=1);

namespace Tests\Unit\Services\Category;

use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;
use App\Exceptions\CategoryNotFoundException;
use App\Models\Article;
use App\Models\Category;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use App\Services\Category\CategoryService;
use App\Services\Pagination\PaginatorService;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;

final class CategoryServiceTest extends TestCase
{
    public function testItBuildsSortedCategoryPage(): void
    {
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $article = new Article(
            imageUrl: 'https://example.com/article.webp',
            title: 'Test article',
            slug: 'test-article',
            description: 'Test description',
            content: 'Test content',
            publishedAt: new DateTimeImmutable('2026-09-14 10:00:00'),
        );
        $categoryRepository = self::createStub(CategoryRepositoryInterface::class);
        $categoryRepository
            ->method('findCategoryBySlug')
            ->willReturn($category);
        $articleRepository = $this->createMock(ArticleRepositoryInterface::class);
        $articleRepository
            ->method('countArticlesByCategory')
            ->willReturn(8);
        $articleRepository
            ->expects($this->once())
            ->method('findArticlesByCategory')
            ->with($category, ArticleSortEnum::Views, SortDirectionEnum::Asc, 6, 6)
            ->willReturn([$article]);

        $categoryService = new CategoryService($categoryRepository, $articleRepository, new PaginatorService());
        $categoryPageDTO = $categoryService->getCategoryPage('technology', ArticleSortEnum::Views, SortDirectionEnum::Asc, 2);

        self::assertSame($category, $categoryPageDTO->category);
        self::assertSame([$article], $categoryPageDTO->articles);
        self::assertSame(ArticleSortEnum::Views, $categoryPageDTO->sort);
        self::assertSame(SortDirectionEnum::Asc, $categoryPageDTO->direction);
        self::assertSame(2, $categoryPageDTO->currentPage);
        self::assertSame(2, $categoryPageDTO->totalPages);
        self::assertSame('views', $categoryPageDTO->categoryNavigationDTO->activeSort);
        self::assertSame('/categories/technology', $categoryPageDTO->categoryNavigationDTO->viewsSortUrl);
        self::assertSame('/categories/technology?sort=date&direction=desc', $categoryPageDTO->categoryNavigationDTO->dateSortUrl);
        self::assertSame([
            ['number' => 1, 'url' => '/categories/technology?sort=views&direction=asc'],
            ['number' => 2, 'url' => '/categories/technology?page=2&sort=views&direction=asc'],
        ], $categoryPageDTO->categoryNavigationDTO->pageLinks);
        self::assertSame('/categories/technology?sort=views&direction=asc', $categoryPageDTO->categoryNavigationDTO->previousPageUrl);
        self::assertNull($categoryPageDTO->categoryNavigationDTO->nextPageUrl);
    }

    public function testItThrowsWhenCategoryDoesNotExist(): void
    {
        $categoryRepository = self::createStub(CategoryRepositoryInterface::class);
        $categoryRepository
            ->method('findCategoryBySlug')
            ->willReturn(null);
        $articleRepository = self::createStub(ArticleRepositoryInterface::class);
        $categoryService = new CategoryService($categoryRepository, $articleRepository, new PaginatorService());

        $this->expectException(CategoryNotFoundException::class);

        $categoryService->getCategoryPage('missing-category');
    }
}
