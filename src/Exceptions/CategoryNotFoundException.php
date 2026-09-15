<?php

declare(strict_types=1);

namespace App\Exceptions;

use RuntimeException;

final class CategoryNotFoundException extends RuntimeException
{
    public function __construct(string $categorySlug)
    {
        parent::__construct(sprintf('Category with slug "%s" was not found.', $categorySlug));
    }
}
