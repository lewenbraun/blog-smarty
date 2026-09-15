<?php

declare(strict_types=1);

namespace App\Services\Article\Contracts;

use App\DTO\Article\ArticlePageDTO;

interface ArticleServiceInterface
{
    public function getArticlePage(string $articleSlug): ArticlePageDTO;
}
