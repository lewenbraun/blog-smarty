<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class ArticleNotFoundException extends RuntimeException
{
    public function __construct(string $articleSlug)
    {
        parent::__construct(sprintf('Article with slug "%s" was not found.', $articleSlug));
    }
}
