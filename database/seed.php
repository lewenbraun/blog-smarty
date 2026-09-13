<?php

declare(strict_types=1);

namespace Database;

use DateInterval;
use DateTimeImmutable;
use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;
use Faker\Factory;
use RuntimeException;
use Throwable;

require dirname(__DIR__) . '/vendor/autoload.php';

$applicationEnvironment = getenv('APP_ENV');
$applicationEnvironment = $applicationEnvironment === false ? 'development' : $applicationEnvironment;

if (!in_array($applicationEnvironment, ['development', 'testing'], true)) {
    fwrite(STDERR, "Database seeding is only allowed in development and testing environments.\n");

    exit(1);
}

/** @var list<array{name: string, slug: string, description: string}> $categories */
$categories = [
    [
        'name' => 'Technology',
        'slug' => 'technology',
        'description' => 'Practical ideas about software, tools, and the systems behind modern products.',
    ],
    [
        'name' => 'Productivity',
        'slug' => 'productivity',
        'description' => 'Simple methods for focused work, better decisions, and sustainable routines.',
    ],
    [
        'name' => 'Business',
        'slug' => 'business',
        'description' => 'Lessons from building products, serving customers, and working in small teams.',
    ],
    [
        'name' => 'Design',
        'slug' => 'design',
        'description' => 'Thoughts on useful interfaces, visual systems, and thoughtful defaults.',
    ],
    [
        'name' => 'Travel',
        'slug' => 'travel',
        'description' => 'Stories about unfamiliar places, slow journeys, and memorable landscapes.',
    ],
    [
        'name' => 'Lifestyle',
        'slug' => 'lifestyle',
        'description' => 'Everyday observations about attention, habits, culture, and a balanced life.',
    ],
];

/**
 * @var list<array{
 *     title: string,
 *     slug: string,
 *     description: string,
 *     imageId: int,
 *     categorySlugs: list<string>
 * }> $articles
 */
$articles = [
    [
        'title' => 'Building a Calm Development Environment',
        'slug' => 'building-a-calm-development-environment',
        'description' => 'A practical approach to reducing friction and keeping everyday development work focused.',
        'imageId' => 0,
        'categorySlugs' => ['technology', 'productivity'],
    ],
    [
        'title' => 'What Makes a Useful Code Review',
        'slug' => 'what-makes-a-useful-code-review',
        'description' => 'Good reviews improve both the code and the shared understanding behind it.',
        'imageId' => 7,
        'categorySlugs' => ['technology', 'business'],
    ],
    [
        'title' => 'Small Automations That Save an Hour',
        'slug' => 'small-automations-that-save-an-hour',
        'description' => 'A collection of modest workflow improvements that quietly add up over time.',
        'imageId' => 3,
        'categorySlugs' => ['technology', 'productivity'],
    ],
    [
        'title' => 'Designing APIs People Can Understand',
        'slug' => 'designing-apis-people-can-understand',
        'description' => 'Clear names and predictable behavior matter more than clever abstractions.',
        'imageId' => 20,
        'categorySlugs' => ['technology', 'design'],
    ],
    [
        'title' => 'The Case for Boring Technology',
        'slug' => 'the-case-for-boring-technology',
        'description' => 'Proven tools often leave more time for the product problems that actually matter.',
        'imageId' => 26,
        'categorySlugs' => ['technology', 'business'],
    ],
    [
        'title' => 'Learning a New Codebase Without Guessing',
        'slug' => 'learning-a-new-codebase-without-guessing',
        'description' => 'A repeatable way to discover structure, constraints, and intent in unfamiliar software.',
        'imageId' => 1,
        'categorySlugs' => ['technology', 'productivity'],
    ],
    [
        'title' => 'From Prototype to Reliable Product',
        'slug' => 'from-prototype-to-reliable-product',
        'description' => 'The engineering priorities that change when an experiment becomes a real service.',
        'imageId' => 36,
        'categorySlugs' => ['technology', 'business'],
    ],
    [
        'title' => 'A Practical Guide to Better Web Performance',
        'slug' => 'a-practical-guide-to-better-web-performance',
        'description' => 'Measure the experience first, then fix the delays that users can actually notice.',
        'imageId' => 35,
        'categorySlugs' => ['technology', 'design'],
    ],
    [
        'title' => 'Choosing Tools for a Small Team',
        'slug' => 'choosing-tools-for-a-small-team',
        'description' => 'The best tool is often the one the whole team can operate and explain.',
        'imageId' => 8,
        'categorySlugs' => ['technology', 'business'],
    ],
    [
        'title' => 'Notes on Working With Legacy Software',
        'slug' => 'notes-on-working-with-legacy-software',
        'description' => 'How to make careful progress when the safest path is not a complete rewrite.',
        'imageId' => 39,
        'categorySlugs' => ['technology', 'productivity'],
    ],
    [
        'title' => 'A Weekend Along the Northern Coast',
        'slug' => 'a-weekend-along-the-northern-coast',
        'description' => 'A quiet route through small beaches, open water, and towns shaped by the sea.',
        'imageId' => 10,
        'categorySlugs' => ['travel', 'lifestyle'],
    ],
    [
        'title' => 'Hiking to the Waterfall Before Sunrise',
        'slug' => 'hiking-to-the-waterfall-before-sunrise',
        'description' => 'An early walk through the forest rewards a little planning and a slower pace.',
        'imageId' => 15,
        'categorySlugs' => ['travel', 'lifestyle'],
    ],
    [
        'title' => 'The Quiet Routes Through the Mountains',
        'slug' => 'the-quiet-routes-through-the-mountains',
        'description' => 'Why the less direct trail can become the most memorable part of a journey.',
        'imageId' => 29,
        'categorySlugs' => ['travel'],
    ],
    [
        'title' => 'A Working Week in a Small Island Town',
        'slug' => 'a-working-week-in-a-small-island-town',
        'description' => 'What changes when ordinary work continues somewhere completely unfamiliar.',
        'imageId' => 49,
        'categorySlugs' => ['travel', 'productivity'],
    ],
    [
        'title' => 'What Slow Travel Teaches About Attention',
        'slug' => 'what-slow-travel-teaches-about-attention',
        'description' => 'Staying longer creates room to notice the details that busy itineraries miss.',
        'imageId' => 17,
        'categorySlugs' => ['travel', 'lifestyle'],
    ],
    [
        'title' => 'Designing Interfaces With Less Noise',
        'slug' => 'designing-interfaces-with-less-noise',
        'description' => 'Removing visual competition helps the important actions become easier to find.',
        'imageId' => 24,
        'categorySlugs' => ['design', 'productivity'],
    ],
    [
        'title' => 'Finding a Visual Rhythm in Everyday Objects',
        'slug' => 'finding-a-visual-rhythm-in-everyday-objects',
        'description' => 'Ordinary shapes and repeated details offer useful lessons in composition.',
        'imageId' => 25,
        'categorySlugs' => ['design', 'lifestyle'],
    ],
    [
        'title' => 'Why Good Defaults Matter',
        'slug' => 'why-good-defaults-matter',
        'description' => 'Thoughtful starting points make products easier without taking control away.',
        'imageId' => 48,
        'categorySlugs' => ['design', 'technology'],
    ],
    [
        'title' => 'The First Ten Customers Change the Product',
        'slug' => 'the-first-ten-customers-change-the-product',
        'description' => 'Early conversations reveal which assumptions survive contact with real needs.',
        'imageId' => 27,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Running Better Meetings With Written Context',
        'slug' => 'running-better-meetings-with-written-context',
        'description' => 'A short document before the call can make discussion faster and decisions clearer.',
        'imageId' => 22,
        'categorySlugs' => ['business', 'productivity'],
    ],
];

/** @var EntityManagerInterface $entityManager */
$entityManager = require dirname(__DIR__) . '/config/doctrine.php';
$connection = $entityManager->getConnection();
$faker = Factory::create('en_US');
$faker->seed(20260912);
$publicationAnchor = new DateTimeImmutable('2026-09-11 12:00:00');

try {
    $connection->beginTransaction();

    /** @var array<string, int> $categoryIdsBySlug */
    $categoryIdsBySlug = [];

    foreach ($categories as $category) {
        $categoryIdsBySlug[$category['slug']] = upsertCategory($connection, $category);
    }

    $articleCategoryCount = 0;

    foreach ($articles as $articleIndex => $article) {
        $articleContent = $faker->paragraphs(5, true);

        if (!is_string($articleContent)) {
            throw new RuntimeException('Faker did not generate article content as text.');
        }

        $publishedAt = $publicationAnchor->sub(new DateInterval(sprintf('P%dD', $articleIndex * 3)));
        $articleId = upsertArticle($connection, [
            'imageUrl' => sprintf('https://picsum.photos/id/%d/960/640.webp', $article['imageId']),
            'title' => $article['title'],
            'slug' => $article['slug'],
            'description' => $article['description'],
            'content' => $articleContent,
            'publishedAt' => $publishedAt->format('Y-m-d H:i:s'),
            'views' => $faker->numberBetween(50, 5000),
        ]);

        $connection->delete('article_category', ['article_id' => $articleId]);

        foreach ($article['categorySlugs'] as $categorySlug) {
            if (!array_key_exists($categorySlug, $categoryIdsBySlug)) {
                throw new RuntimeException(sprintf('Unknown category slug "%s".', $categorySlug));
            }

            $connection->insert('article_category', [
                'article_id' => $articleId,
                'category_id' => $categoryIdsBySlug[$categorySlug],
            ]);
            ++$articleCategoryCount;
        }
    }

    $connection->commit();
} catch (Throwable $throwable) {
    if ($connection->isTransactionActive()) {
        $connection->rollBack();
    }

    fwrite(STDERR, sprintf("Database seeding failed: %s\n", $throwable->getMessage()));

    exit(1);
}

fwrite(
    STDOUT,
    sprintf(
        "Seeded %d categories, %d articles, and %d article-category relations.\n",
        count($categories),
        count($articles),
        $articleCategoryCount,
    ),
);

/**
 * @param array{name: string, slug: string, description: string} $category
 */
function upsertCategory(Connection $connection, array $category): int
{
    $categoryId = $connection->fetchOne(
        'SELECT id FROM categories WHERE slug = :slug',
        ['slug' => $category['slug']],
    );

    $categoryValues = [
        'name' => $category['name'],
        'slug' => $category['slug'],
        'description' => $category['description'],
    ];

    if ($categoryId === false) {
        $connection->insert('categories', $categoryValues);

        return (int) $connection->lastInsertId();
    }

    $validatedCategoryId = filter_var($categoryId, FILTER_VALIDATE_INT);

    if (!is_int($validatedCategoryId)) {
        throw new RuntimeException('Category ID must be an integer.');
    }

    $connection->update('categories', $categoryValues, ['id' => $validatedCategoryId]);

    return $validatedCategoryId;
}

/**
 * @param array{
 *     imageUrl: string,
 *     title: string,
 *     slug: string,
 *     description: string,
 *     content: string,
 *     publishedAt: string,
 *     views: int
 * } $article
 */
function upsertArticle(Connection $connection, array $article): int
{
    $articleId = $connection->fetchOne(
        'SELECT id FROM articles WHERE slug = :slug',
        ['slug' => $article['slug']],
    );

    $articleValues = [
        'image_url' => $article['imageUrl'],
        'title' => $article['title'],
        'slug' => $article['slug'],
        'description' => $article['description'],
        'content' => $article['content'],
        'published_at' => $article['publishedAt'],
        'views' => $article['views'],
    ];

    if ($articleId === false) {
        $connection->insert('articles', $articleValues);

        return (int) $connection->lastInsertId();
    }

    $validatedArticleId = filter_var($articleId, FILTER_VALIDATE_INT);

    if (!is_int($validatedArticleId)) {
        throw new RuntimeException('Article ID must be an integer.');
    }

    $connection->update('articles', $articleValues, ['id' => $validatedArticleId]);

    return $validatedArticleId;
}
