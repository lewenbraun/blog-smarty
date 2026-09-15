<?php

declare(strict_types=1);

namespace App\Services\Category;

use App\DTO\Category\CategoryNavigationDTO;
use App\DTO\Category\CategoryPageDTO;
use App\DTO\Pagination\PaginationDTO;
use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;
use App\Exceptions\CategoryNotFoundException;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use App\Services\Category\Contracts\CategoryServiceInterface;
use App\Services\Pagination\Contracts\PaginatorServiceInterface;

final readonly class CategoryService implements CategoryServiceInterface
{
    private const int ARTICLES_PER_PAGE = 6;

    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ArticleRepositoryInterface $articleRepository,
        private PaginatorServiceInterface $paginatorService,
    ) {}

    public function getCategoryPage(
        string $categorySlug,
        ?ArticleSortEnum $sort = null,
        SortDirectionEnum $direction = SortDirectionEnum::Desc,
        int $page = 1,
    ): CategoryPageDTO {
        $category = $this->categoryRepository->findCategoryBySlug($categorySlug);

        if ($category === null) {
            throw new CategoryNotFoundException($categorySlug);
        }

        $totalArticles = $this->articleRepository->countArticlesByCategory($category);
        $paginationDTO = $this->paginatorService->getPagination($totalArticles, self::ARTICLES_PER_PAGE, $page);

        if ($sort === null) {
            $direction = SortDirectionEnum::Desc;
        }

        $articles = $this->articleRepository->findArticlesByCategory(
            category: $category,
            sort: $sort ?? ArticleSortEnum::Date,
            direction: $direction,
            limit: $paginationDTO->limit,
            offset: $paginationDTO->offset,
        );

        return new CategoryPageDTO(
            category: $category,
            articles: $articles,
            sort: $sort,
            direction: $direction,
            currentPage: $paginationDTO->currentPage,
            totalPages: $paginationDTO->totalPages,
            categoryNavigationDTO: $this->getCategoryNavigation($category->getSlug(), $sort, $direction, $paginationDTO),
        );
    }

    private function getCategoryNavigation(
        string $categorySlug,
        ?ArticleSortEnum $sort,
        SortDirectionEnum $direction,
        PaginationDTO $paginationDTO,
    ): CategoryNavigationDTO {
        $categoryPath = '/categories/' . rawurlencode($categorySlug);
        $pageLinks = [];

        for ($page = 1; $page <= $paginationDTO->totalPages; ++$page) {
            $pageLinks[] = [
                'number' => $page,
                'url' => $this->pageUrl($categoryPath, $sort, $direction, $page),
            ];
        }

        $activeSort = '';
        $previousPageUrl = null;
        $nextPageUrl = null;

        if ($sort !== null) {
            $activeSort = $sort->value;
        }

        if ($paginationDTO->currentPage > 1) {
            $previousPageUrl = $this->pageUrl($categoryPath, $sort, $direction, $paginationDTO->currentPage - 1);
        }

        if ($paginationDTO->currentPage < $paginationDTO->totalPages) {
            $nextPageUrl = $this->pageUrl($categoryPath, $sort, $direction, $paginationDTO->currentPage + 1);
        }

        return new CategoryNavigationDTO(
            activeSort: $activeSort,
            viewsSortUrl: $this->sortUrl($categoryPath, $sort, $direction, ArticleSortEnum::Views),
            dateSortUrl: $this->sortUrl($categoryPath, $sort, $direction, ArticleSortEnum::Date),
            pageLinks: $pageLinks,
            previousPageUrl: $previousPageUrl,
            nextPageUrl: $nextPageUrl,
        );
    }

    private function sortUrl(
        string $categoryPath,
        ?ArticleSortEnum $activeSort,
        SortDirectionEnum $direction,
        ArticleSortEnum $sort,
    ): string {
        if ($activeSort === $sort && $direction === SortDirectionEnum::Asc) {
            return $categoryPath;
        }

        $nextDirection = SortDirectionEnum::Desc;

        if ($activeSort === $sort) {
            $nextDirection = SortDirectionEnum::Asc;
        }

        return $categoryPath . '?' . http_build_query([
            'sort' => $sort->value,
            'direction' => $nextDirection->value,
        ]);
    }

    private function pageUrl(
        string $categoryPath,
        ?ArticleSortEnum $sort,
        SortDirectionEnum $direction,
        int $page,
    ): string {
        $queryParameters = [];

        if ($page > 1) {
            $queryParameters['page'] = $page;
        }

        if ($sort !== null) {
            $queryParameters['sort'] = $sort->value;
            $queryParameters['direction'] = $direction->value;
        }

        if ($queryParameters === []) {
            return $categoryPath;
        }

        return $categoryPath . '?' . http_build_query($queryParameters);
    }
}
