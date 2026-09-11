<?php

declare(strict_types=1);

namespace App\Models;

use DateTimeImmutable;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'articles')]
#[ORM\UniqueConstraint(name: 'uniq_articles_slug', columns: ['slug'])]
#[ORM\Index(name: 'idx_articles_published_at_id', columns: ['published_at', 'id'])]
#[ORM\Index(name: 'idx_articles_views_id', columns: ['views', 'id'])]
class Article
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    /** @var Collection<int, Category> */
    #[ORM\ManyToMany(targetEntity: Category::class)]
    #[ORM\JoinTable(name: 'article_category')]
    #[ORM\JoinColumn(
        name: 'article_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
        options: ['unsigned' => true],
    )]
    #[ORM\InverseJoinColumn(
        name: 'category_id',
        referencedColumnName: 'id',
        nullable: false,
        onDelete: 'CASCADE',
        options: ['unsigned' => true],
    )]
    private Collection $categories;

    public function __construct(
        #[ORM\Column(name: 'image_url', length: 2048)]
        private string $imageUrl,
        #[ORM\Column(length: 255)]
        private string $title,
        #[ORM\Column(length: 255)]
        private string $slug,
        #[ORM\Column(type: Types::TEXT)]
        private string $description,
        #[ORM\Column(type: Types::TEXT)]
        private string $content,
        #[ORM\Column(name: 'published_at', type: Types::DATETIME_IMMUTABLE)]
        private DateTimeImmutable $publishedAt,
        #[ORM\Column(options: ['unsigned' => true, 'default' => 0])]
        private int $views = 0,
    ) {
        $this->categories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getContent(): string
    {
        return $this->content;
    }

    public function getPublishedAt(): DateTimeImmutable
    {
        return $this->publishedAt;
    }

    public function getViews(): int
    {
        return $this->views;
    }

    /** @return list<Category> */
    public function getCategories(): array
    {
        return $this->categories->getValues();
    }

}
