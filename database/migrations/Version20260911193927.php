<?php

declare(strict_types=1);

namespace Database\Migrations;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Migrations\AbstractMigration;

final class Version20260911193927 extends AbstractMigration
{
    public function getDescription(): string
    {
        return 'Create categories, articles, and their many-to-many relation';
    }

    public function up(Schema $schema): void
    {
        $this->addSql(
            'CREATE TABLE categories (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                name VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                UNIQUE INDEX uniq_categories_slug (slug),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB',
        );
        $this->addSql(
            'CREATE TABLE articles (
                id INT UNSIGNED AUTO_INCREMENT NOT NULL,
                image_url VARCHAR(2048) NOT NULL,
                title VARCHAR(255) NOT NULL,
                slug VARCHAR(255) NOT NULL,
                description LONGTEXT NOT NULL,
                content LONGTEXT NOT NULL,
                published_at DATETIME NOT NULL,
                views INT UNSIGNED DEFAULT 0 NOT NULL,
                UNIQUE INDEX uniq_articles_slug (slug),
                INDEX idx_articles_published_at_id (published_at, id),
                INDEX idx_articles_views_id (views, id),
                PRIMARY KEY (id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB',
        );
        $this->addSql(
            'CREATE TABLE article_category (
                article_id INT UNSIGNED NOT NULL,
                category_id INT UNSIGNED NOT NULL,
                INDEX IDX_53A4EDAA7294869C (article_id),
                INDEX IDX_53A4EDAA12469DE2 (category_id),
                PRIMARY KEY (article_id, category_id)
            ) DEFAULT CHARACTER SET utf8mb4 COLLATE `utf8mb4_0900_ai_ci` ENGINE = InnoDB',
        );
        $this->addSql(
            'ALTER TABLE article_category
                ADD CONSTRAINT FK_ARTICLE_CATEGORY_ARTICLE
                FOREIGN KEY (article_id) REFERENCES articles (id) ON DELETE CASCADE',
        );
        $this->addSql(
            'ALTER TABLE article_category
                ADD CONSTRAINT FK_ARTICLE_CATEGORY_CATEGORY
                FOREIGN KEY (category_id) REFERENCES categories (id) ON DELETE CASCADE',
        );
    }

    public function down(Schema $schema): void
    {
        $this->addSql('DROP TABLE article_category');
        $this->addSql('DROP TABLE articles');
        $this->addSql('DROP TABLE categories');
    }
}
