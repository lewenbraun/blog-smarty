<?php

declare(strict_types=1);

namespace App\Enums;

enum ArticleSortEnum: string
{
    case Date = 'date';
    case Views = 'views';
}
