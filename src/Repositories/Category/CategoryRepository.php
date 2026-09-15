<?php

declare(strict_types=1);

namespace App\Repositories\Category;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Category\Contracts\CategoryRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

final readonly class CategoryRepository implements CategoryRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    /** @return list<Category> */
    public function findCategoriesWithArticles(): array
    {
        $categoryIds = $this->findCategoryIdsWithArticles();

        if ($categoryIds === []) {
            return [];
        }

        /** @var list<Category> $categories */
        $categories = $this->entityManager
            ->getRepository(Category::class)
            ->findBy(['id' => $categoryIds], ['name' => 'ASC']);

        return $categories;
    }

    /** @return list<int> */
    private function findCategoryIdsWithArticles(): array
    {
        /** @var list<int|string> $categoryIds */
        $categoryIds = $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT category.id')
            ->from(Article::class, 'article')
            ->innerJoin('article.categories', 'category')
            ->getQuery()
            ->getSingleColumnResult();

        return array_map(
            static fn(int|string $categoryId): int => (int) $categoryId,
            $categoryIds,
        );
    }
}
