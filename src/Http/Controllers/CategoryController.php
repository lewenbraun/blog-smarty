<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\CategoryNotFoundException;
use App\Http\Requests\CategoryPageRequest;
use App\Services\Category\Contracts\CategoryServiceInterface;
use Smarty\Smarty;

final readonly class CategoryController
{
    public function __construct(
        private Smarty $smarty,
        private CategoryServiceInterface $categoryService,
    ) {}

    public function show(string $categorySlug): void
    {
        $categoryPageRequest = CategoryPageRequest::fromGlobals();

        try {
            $categoryPageDTO = $this->categoryService->getCategoryPage(
                categorySlug: $categorySlug,
                sort: $categoryPageRequest->sort,
                direction: $categoryPageRequest->direction,
                page: $categoryPageRequest->page,
            );
        } catch (CategoryNotFoundException) {
            http_response_code(404);
            $this->smarty->display('errors/404.tpl');

            return;
        }

        $categoryNavigationDTO = $categoryPageDTO->categoryNavigationDTO;

        $this->smarty->assign([
            'category' => $categoryPageDTO->category,
            'articles' => $categoryPageDTO->articles,
            'activeSort' => $categoryNavigationDTO->activeSort,
            'sortDirection' => $categoryPageDTO->direction->value,
            'viewsSortUrl' => $categoryNavigationDTO->viewsSortUrl,
            'dateSortUrl' => $categoryNavigationDTO->dateSortUrl,
            'currentPage' => $categoryPageDTO->currentPage,
            'totalPages' => $categoryPageDTO->totalPages,
            'pageLinks' => $categoryNavigationDTO->pageLinks,
            'previousPageUrl' => $categoryNavigationDTO->previousPageUrl,
            'nextPageUrl' => $categoryNavigationDTO->nextPageUrl,
        ]);
        $this->smarty->display('category/show.tpl');
    }
}
