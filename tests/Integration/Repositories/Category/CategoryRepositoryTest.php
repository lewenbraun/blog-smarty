<?php

declare(strict_types=1);

namespace Tests\Integration\Repositories\Category;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Category\CategoryRepository;
use DateTimeImmutable;
use Tests\IntegrationTestCase;

final class CategoryRepositoryTest extends IntegrationTestCase
{
    public function testItFindsCategoryBySlug(): void
    {
        $entityManager = $this->entityManager();
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $entityManager->persist($category);
        $entityManager->flush();
        $entityManager->clear();

        $categoryRepository = new CategoryRepository($entityManager);
        $storedCategory = $categoryRepository->findCategoryBySlug('technology');

        self::assertInstanceOf(Category::class, $storedCategory);
        self::assertSame('Technology', $storedCategory->getName());
        self::assertSame('Technology articles.', $storedCategory->getDescription());
        self::assertNull($categoryRepository->findCategoryBySlug('missing-category'));
    }

    public function testItFindsOnlyCategoriesWithArticlesWithoutDuplicates(): void
    {
        $entityManager = $this->entityManager();
        $technologyCategory = new Category('Technology', 'technology', 'Technology articles.');
        $businessCategory = new Category('Business', 'business', 'Business articles.');
        $emptyCategory = new Category('Design', 'design', 'Design articles.');
        $firstArticle = $this->createArticle('first-article');
        $secondArticle = $this->createArticle('second-article');

        foreach ([$technologyCategory, $businessCategory, $emptyCategory] as $category) {
            $entityManager->persist($category);
        }

        foreach ([$firstArticle, $secondArticle] as $article) {
            $entityManager->persist($article);
        }

        $entityManager->flush();

        foreach ([$technologyCategory, $businessCategory] as $category) {
            $categoryId = $category->getId();
            self::assertNotNull($categoryId);

            foreach ([$firstArticle, $secondArticle] as $article) {
                $articleId = $article->getId();
                self::assertNotNull($articleId);

                $entityManager->getConnection()->insert('article_category', [
                    'article_id' => $articleId,
                    'category_id' => $categoryId,
                ]);
            }
        }

        $entityManager->clear();

        $categoryRepository = new CategoryRepository($entityManager);
        $categories = $categoryRepository->findCategoriesWithArticles();

        self::assertSame(
            ['business', 'technology'],
            array_map(
                static fn(Category $category): string => $category->getSlug(),
                $categories,
            ),
        );
    }

    public function testItReturnsNoCategoriesWhenNoneHaveArticles(): void
    {
        $entityManager = $this->entityManager();
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $article = $this->createArticle('uncategorized-article');
        $entityManager->persist($category);
        $entityManager->persist($article);
        $entityManager->flush();
        $entityManager->clear();

        $categoryRepository = new CategoryRepository($entityManager);

        self::assertSame([], $categoryRepository->findCategoriesWithArticles());
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
