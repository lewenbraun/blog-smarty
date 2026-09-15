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

    private function createArticle(string $articleSlug, int $articleViews = 0): Article
    {
        return new Article(
            imageUrl: 'https://example.com/article.webp',
            title: 'Test article',
            slug: $articleSlug,
            description: 'Test description',
            content: 'Test content',
            publishedAt: new DateTimeImmutable('2026-09-14 10:00:00'),
            views: $articleViews,
        );
    }
}
