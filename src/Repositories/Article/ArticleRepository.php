<?php

declare(strict_types=1);

namespace App\Repositories\Article;

use App\Models\Article;
use App\Models\Category;
use App\Repositories\Article\Contracts\ArticleRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;
use LogicException;

final readonly class ArticleRepository implements ArticleRepositoryInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
    ) {}

    public function findArticleBySlug(string $articleSlug): ?Article
    {
        $article = $this->entityManager
            ->getRepository(Article::class)
            ->findOneBy(['slug' => $articleSlug]);

        return $article;
    }

    public function incrementArticleViews(Article $article): void
    {
        $articleId = $this->getArticleIdOrFail($article);

        $this->entityManager
            ->createQueryBuilder()
            ->update(Article::class, 'article')
            ->set('article.views', 'article.views + 1')
            ->where('article.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()
            ->execute();

        $this->entityManager->refresh($article);
    }

    /**
     * @param positive-int $limit
     *
     * @return list<Article>
     */
    public function findSimilarArticles(Article $article, int $limit): array
    {
        $articleId = $this->getArticleIdOrFail($article);
        $articleCategoryIds = $this->findArticleCategoryIds($articleId);

        if ($articleCategoryIds === []) {
            return [];
        }

        return $this->findSimilarArticlesByCategoryIds(
            articleId: $articleId,
            articleCategoryIds: $articleCategoryIds,
            limit: $limit,
        );
    }

    /**
     * @param positive-int $limit
     *
     * @return list<Article>
     */
    public function findLatestArticlesByCategory(Category $category, int $limit): array
    {
        $categoryId = $this->getCategoryIdOrFail($category);

        /** @var list<Article> $latestArticles */
        $latestArticles = $this->entityManager
            ->createQueryBuilder()
            ->select('article')
            ->from(Article::class, 'article')
            ->innerJoin('article.categories', 'category')
            ->where('category.id = :categoryId')
            ->setParameter('categoryId', $categoryId)
            ->orderBy('article.publishedAt', 'DESC')
            ->addOrderBy('article.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $latestArticles;
    }

    /** @return list<int> */
    private function findArticleCategoryIds(int $articleId): array
    {
        /** @var list<int|string> $articleCategoryIds */
        $articleCategoryIds = $this->entityManager
            ->createQueryBuilder()
            ->select('category.id')
            ->from(Article::class, 'article')
            ->innerJoin('article.categories', 'category')
            ->where('article.id = :articleId')
            ->setParameter('articleId', $articleId)
            ->getQuery()
            ->getSingleColumnResult();

        return array_map(
            static fn(int|string $categoryId): int => (int) $categoryId,
            $articleCategoryIds,
        );
    }

    /**
     * @param list<int>    $articleCategoryIds
     * @param positive-int $limit
     *
     * @return list<Article>
     */
    private function findSimilarArticlesByCategoryIds(
        int $articleId,
        array $articleCategoryIds,
        int $limit,
    ): array {
        /** @var list<Article> $similarArticles */
        $similarArticles = $this->entityManager
            ->createQueryBuilder()
            ->select('DISTINCT similarArticle')
            ->from(Article::class, 'similarArticle')
            ->innerJoin('similarArticle.categories', 'category')
            ->where('category.id IN (:articleCategoryIds)')
            ->andWhere('similarArticle.id != :articleId')
            ->setParameter('articleCategoryIds', $articleCategoryIds)
            ->setParameter('articleId', $articleId)
            ->orderBy('similarArticle.publishedAt', 'DESC')
            ->addOrderBy('similarArticle.id', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();

        return $similarArticles;
    }

    private function getArticleIdOrFail(Article $article): int
    {
        $articleId = $article->getId();

        if ($articleId === null) {
            throw new LogicException('A persisted article must have an ID.');
        }

        return $articleId;
    }

    private function getCategoryIdOrFail(Category $category): int
    {
        $categoryId = $category->getId();

        if ($categoryId === null) {
            throw new LogicException('A persisted category must have an ID.');
        }

        return $categoryId;
    }
}
