<?php

declare(strict_types=1);

namespace App\Services\Home;

use App\DTO\Home\CategoryArticlesDTO;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use App\Services\Home\Contracts\HomeServiceInterface;

final readonly class HomeService implements HomeServiceInterface
{
    private const int LATEST_ARTICLES_PER_CATEGORY = 3;

    public function __construct(
        private CategoryRepositoryInterface $categoryRepository,
        private ArticleRepositoryInterface $articleRepository,
    ) {}

    public function getHomePage(): array
    {
        $categories = $this->categoryRepository->findCategoriesWithArticles();

        $categoriesWithLatestArticles = [];

        foreach ($categories as $category) {
            $categoriesWithLatestArticles[] = new CategoryArticlesDTO(
                category: $category,
                articles: $this->articleRepository->findLatestArticlesByCategory(
                    $category,
                    self::LATEST_ARTICLES_PER_CATEGORY,
                ),
            );
        }

        return $categoriesWithLatestArticles;
    }
}
