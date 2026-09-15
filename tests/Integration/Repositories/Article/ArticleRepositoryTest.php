<?php

declare(strict_types=1);

namespace Tests\Integration\Repositories\Article;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Article\ArticleRepository;
use DateTimeImmutable;
use Tests\IntegrationTestCase;

final class ArticleRepositoryTest extends IntegrationTestCase
{
    public function testItFindsArticleAndIncrementsViews(): void
    {
        $entityManager = $this->entityManager();
        $article = $this->createArticle('test-article', 7);
        $entityManager->persist($article);
        $entityManager->flush();
        $entityManager->clear();

        $articleRepository = new ArticleRepository($entityManager);
        $storedArticle = $articleRepository->findArticleBySlug('test-article');

        self::assertInstanceOf(Article::class, $storedArticle);

        $articleRepository->incrementArticleViews($storedArticle);

        self::assertSame(8, $storedArticle->getViews());
    }

    public function testItFindsArticleWithSharedCategoryAsSimilar(): void
    {
        $entityManager = $this->entityManager();
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $article = $this->createArticle('main-article');
        $similarArticle = $this->createArticle('similar-article');
        $unrelatedArticle = $this->createArticle('unrelated-article');

        $entityManager->persist($category);
        $entityManager->persist($article);
        $entityManager->persist($similarArticle);
        $entityManager->persist($unrelatedArticle);
        $entityManager->flush();

        $articleId = $article->getId();
        $similarArticleId = $similarArticle->getId();
        $categoryId = $category->getId();

        self::assertNotNull($articleId);
        self::assertNotNull($similarArticleId);
        self::assertNotNull($categoryId);

        $connection = $entityManager->getConnection();
        $connection->insert('article_category', [
            'article_id' => $articleId,
            'category_id' => $categoryId,
        ]);
        $connection->insert('article_category', [
            'article_id' => $similarArticleId,
            'category_id' => $categoryId,
        ]);

        $articleRepository = new ArticleRepository($entityManager);
        $similarArticles = $articleRepository->findSimilarArticles($article, 3);

        self::assertSame(
            ['similar-article'],
            array_map(
                static fn(Article $similarArticle): string => $similarArticle->getSlug(),
                $similarArticles,
            ),
        );
    }

    public function testItFindsLatestArticlesOnlyInRequestedCategory(): void
    {
        $entityManager = $this->entityManager();
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $otherCategory = new Category('Business', 'business', 'Business articles.');
        $oldestArticle = $this->createArticle('oldest-article', 900, '2026-09-10 10:00:00');
        $sameDateArticle = $this->createArticle('same-date-article', 10, '2026-09-13 10:00:00');
        $latestArticle = $this->createArticle('latest-article', 0, '2026-09-13 10:00:00');
        $middleArticle = $this->createArticle('middle-article', 300, '2026-09-12 10:00:00');
        $unrelatedArticle = $this->createArticle('unrelated-article', 1000, '2026-09-15 10:00:00');

        $entityManager->persist($category);
        $entityManager->persist($otherCategory);

        foreach ([$oldestArticle, $sameDateArticle, $latestArticle, $middleArticle, $unrelatedArticle] as $article) {
            $entityManager->persist($article);
        }

        $entityManager->flush();

        foreach ([$oldestArticle, $sameDateArticle, $latestArticle, $middleArticle] as $article) {
            $this->linkArticleToCategory($article, $category);
        }

        $this->linkArticleToCategory($latestArticle, $otherCategory);
        $this->linkArticleToCategory($unrelatedArticle, $otherCategory);
        $categoryId = $category->getId();
        self::assertNotNull($categoryId);
        $entityManager->clear();
        $storedCategory = $entityManager->find(Category::class, $categoryId);
        self::assertInstanceOf(Category::class, $storedCategory);

        $articleRepository = new ArticleRepository($entityManager);
        $latestArticles = $articleRepository->findLatestArticlesByCategory($storedCategory, 3);

        self::assertSame(
            ['latest-article', 'same-date-article', 'middle-article'],
            array_map(
                static fn(Article $article): string => $article->getSlug(),
                $latestArticles,
            ),
        );

        self::assertSame(
            ['latest-article', 'same-date-article'],
            array_map(
                static fn(Article $article): string => $article->getSlug(),
                $articleRepository->findLatestArticlesByCategory($storedCategory, 2),
            ),
        );
    }

    public function testItReturnsNoLatestArticlesForEmptyCategory(): void
    {
        $entityManager = $this->entityManager();
        $category = new Category('Technology', 'technology', 'Technology articles.');
        $entityManager->persist($category);
        $entityManager->flush();

        $articleRepository = new ArticleRepository($entityManager);

        self::assertSame([], $articleRepository->findLatestArticlesByCategory($category, 3));
    }

    private function linkArticleToCategory(Article $article, Category $category): void
    {
        $articleId = $article->getId();
        $categoryId = $category->getId();

        self::assertNotNull($articleId);
        self::assertNotNull($categoryId);

        $this->entityManager()->getConnection()->insert('article_category', [
            'article_id' => $articleId,
            'category_id' => $categoryId,
        ]);
    }

    private function createArticle(
        string $articleSlug,
        int $articleViews = 0,
        string $publishedAt = '2026-09-14 10:00:00',
    ): Article {
        return new Article(
            imageUrl: 'https://example.com/article.webp',
            title: 'Test article',
            slug: $articleSlug,
            description: 'Test description',
            content: 'Test content',
            publishedAt: new DateTimeImmutable($publishedAt),
            views: $articleViews,
        );
    }
}
