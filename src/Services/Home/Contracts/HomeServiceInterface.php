<?php

declare(strict_types=1);

namespace App\Services\Home\Contracts;

use App\DTO\Home\CategoryArticlesDTO;

interface HomeServiceInterface
{
    /** @return list<CategoryArticlesDTO> */
    public function getHomePage(): array;
}
