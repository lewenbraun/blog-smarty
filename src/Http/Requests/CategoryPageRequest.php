<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\ArticleSortEnum;
use App\Enums\SortDirectionEnum;

final readonly class CategoryPageRequest
{
    public function __construct(
        public ?ArticleSortEnum $sort,
        public SortDirectionEnum $direction,
        public int $page,
    ) {}

    public static function fromGlobals(): self
    {
        $sortValue = $_GET['sort'] ?? null;
        $directionValue = $_GET['direction'] ?? null;
        $pageValue = $_GET['page'] ?? '1';

        $sort = null;
        $direction = SortDirectionEnum::Desc;
        $page = 1;

        if (is_string($sortValue)) {
            $sort = ArticleSortEnum::tryFrom($sortValue);
        }

        if ($sort !== null && is_string($directionValue)) {
            $direction = SortDirectionEnum::tryFrom($directionValue) ?? SortDirectionEnum::Desc;
        }

        if (is_string($pageValue)) {
            $validatedPage = filter_var($pageValue, FILTER_VALIDATE_INT);

            if (is_int($validatedPage) && $validatedPage > 0) {
                $page = $validatedPage;
            }
        }

        return new self(
            sort: $sort,
            direction: $direction,
            page: $page,
        );
    }
}
