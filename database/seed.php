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
    [
        'title' => 'Setting a Price for Your First Product',
        'slug' => 'setting-a-price-for-your-first-product',
        'description' => 'Start with costs and customer value before comparing prices with competitors.',
        'imageId' => 20,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Reading Customer Feedback Without Jumping to Conclusions',
        'slug' => 'reading-customer-feedback-without-jumping-to-conclusions',
        'description' => 'Separate repeated problems from individual requests when deciding what to build.',
        'imageId' => 7,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Planning a Budget for a Small Team',
        'slug' => 'planning-a-budget-for-a-small-team',
        'description' => 'A simple budget makes recurring expenses and upcoming commitments easier to see.',
        'imageId' => 3,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Choosing Which Customer Problem to Solve First',
        'slug' => 'choosing-which-customer-problem-to-solve-first',
        'description' => 'Compare frequency, impact, and effort before picking the next product improvement.',
        'imageId' => 26,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Writing a Proposal People Can Act On',
        'slug' => 'writing-a-proposal-people-can-act-on',
        'description' => 'Explain the problem, expected result, cost, and next step in a short proposal.',
        'imageId' => 1,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Making the First Hire in a Growing Business',
        'slug' => 'making-the-first-hire-in-a-growing-business',
        'description' => 'Define the work that needs doing before turning it into a job description.',
        'imageId' => 36,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Understanding Cash Flow Beyond Monthly Revenue',
        'slug' => 'understanding-cash-flow-beyond-monthly-revenue',
        'description' => 'The timing of payments can matter as much as the total income on a report.',
        'imageId' => 35,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'A Useful Weekly Review for Business Owners',
        'slug' => 'a-useful-weekly-review-for-business-owners',
        'description' => 'Check customer issues, commitments, and spending without building a reporting system.',
        'imageId' => 8,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Keeping Product Scope Under Control',
        'slug' => 'keeping-product-scope-under-control',
        'description' => 'Write down what the next release must accomplish before accepting more features.',
        'imageId' => 39,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Preparing for a Customer Interview',
        'slug' => 'preparing-for-a-customer-interview',
        'description' => 'Ask about recent experiences and concrete problems instead of hypothetical purchases.',
        'imageId' => 27,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'When a Partnership Makes Sense',
        'slug' => 'when-a-partnership-makes-sense',
        'description' => 'Check responsibilities, incentives, and shared expectations before agreeing to collaborate.',
        'imageId' => 22,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Measuring Whether Customers Come Back',
        'slug' => 'measuring-whether-customers-come-back',
        'description' => 'Repeat usage reveals whether a product keeps solving a problem after the first visit.',
        'imageId' => 0,
        'categorySlugs' => ['business'],
    ],
    [
        'title' => 'Choosing Typefaces for a Small Website',
        'slug' => 'choosing-typefaces-for-a-small-website',
        'description' => 'Use readable body text and a restrained heading style before adding more fonts.',
        'imageId' => 24,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Building a Consistent Spacing Scale',
        'slug' => 'building-a-consistent-spacing-scale',
        'description' => 'A few repeated distances help related elements feel like parts of the same interface.',
        'imageId' => 25,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Making Form Labels Clearer',
        'slug' => 'making-form-labels-clearer',
        'description' => 'Explain the expected input with visible labels and examples where they help.',
        'imageId' => 48,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Using Color to Show What Matters',
        'slug' => 'using-color-to-show-what-matters',
        'description' => 'Reserve strong colors for meaningful states and actions instead of decoration.',
        'imageId' => 20,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Designing Empty States That Explain the Next Step',
        'slug' => 'designing-empty-states-that-explain-the-next-step',
        'description' => 'An empty screen can explain what belongs there and how to get started.',
        'imageId' => 35,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Checking Contrast Before Shipping',
        'slug' => 'checking-contrast-before-shipping',
        'description' => 'Review text and controls against their actual backgrounds before calling a design finished.',
        'imageId' => 26,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Keeping Navigation Short and Predictable',
        'slug' => 'keeping-navigation-short-and-predictable',
        'description' => 'Group destinations around what visitors are trying to find rather than internal terminology.',
        'imageId' => 1,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Cropping Images for Article Cards',
        'slug' => 'cropping-images-for-article-cards',
        'description' => 'Consistent image proportions make a collection easier to scan across different screens.',
        'imageId' => 36,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Writing Error Messages That Help',
        'slug' => 'writing-error-messages-that-help',
        'description' => 'Say what happened and what the person can do next in ordinary language.',
        'imageId' => 8,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Sketching Layouts Before Choosing Colors',
        'slug' => 'sketching-layouts-before-choosing-colors',
        'description' => 'Simple sketches let you compare content placement without getting distracted by polish.',
        'imageId' => 39,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Choosing Icons That Need No Explanation',
        'slug' => 'choosing-icons-that-need-no-explanation',
        'description' => 'Use familiar symbols and keep text labels when an icon could be misunderstood.',
        'imageId' => 27,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Reviewing an Interface With Real Content',
        'slug' => 'reviewing-an-interface-with-real-content',
        'description' => 'Long titles and uneven descriptions reveal layout problems that placeholders can hide.',
        'imageId' => 22,
        'categorySlugs' => ['design'],
    ],
    [
        'title' => 'Making Time for an Evening Walk',
        'slug' => 'making-time-for-an-evening-walk',
        'description' => 'A short walk can mark the end of the working day and create a little breathing room.',
        'imageId' => 10,
        'categorySlugs' => ['lifestyle'],
    ],
    [
        'title' => 'Cooking a Few Meals for the Week',
        'slug' => 'cooking-a-few-meals-for-the-week',
        'description' => 'Prepare a few flexible ingredients rather than planning every meal in advance.',
        'imageId' => 15,
        'categorySlugs' => ['lifestyle'],
    ],
    [
        'title' => 'Finding a Quiet Place to Read',
        'slug' => 'finding-a-quiet-place-to-read',
        'description' => 'A comfortable seat and fewer interruptions can make reading a regular part of the day.',
        'imageId' => 29,
        'categorySlugs' => ['lifestyle'],
    ],
    [
        'title' => 'Keeping a Small Notebook of Everyday Ideas',
        'slug' => 'keeping-a-small-notebook-of-everyday-ideas',
        'description' => 'Capture observations while they are fresh without turning notes into another task.',
        'imageId' => 49,
        'categorySlugs' => ['lifestyle'],
    ],
    [
        'title' => 'Spending a Weekend Close to Home',
        'slug' => 'spending-a-weekend-close-to-home',
        'description' => 'Familiar neighborhoods can offer new places to explore without a long journey.',
        'imageId' => 17,
        'categorySlugs' => ['lifestyle'],
    ],
    [
        'title' => 'Making Space for a Hobby Without a Schedule',
        'slug' => 'making-space-for-a-hobby-without-a-schedule',
        'description' => 'Keep materials within reach and leave room for doing something simply because you enjoy it.',
        'imageId' => 25,
        'categorySlugs' => ['lifestyle'],
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
