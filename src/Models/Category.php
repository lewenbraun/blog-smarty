<?php

declare(strict_types=1);

namespace App\Models;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'categories')]
#[ORM\UniqueConstraint(name: 'uniq_categories_slug', columns: ['slug'])]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(options: ['unsigned' => true])]
    private ?int $id = null;

    public function __construct(
        #[ORM\Column(length: 255)]
        private string $name,
        #[ORM\Column(length: 255)]
        private string $slug,
        #[ORM\Column(type: Types::TEXT)]
        private string $description,
    ) {}

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getSlug(): string
    {
        return $this->slug;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

}
