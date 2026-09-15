<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Exceptions\ArticleNotFoundException;
use App\Services\Article\Contracts\ArticleServiceInterface;
use Smarty\Smarty;

final readonly class ArticleController
{
    public function __construct(
        private Smarty $smarty,
        private ArticleServiceInterface $articleService,
    ) {}

    public function show(string $articleSlug): void
    {
        try {
            $articlePageDTO = $this->articleService->getArticlePage($articleSlug);
        } catch (ArticleNotFoundException) {
            http_response_code(404);
            $this->smarty->display('errors/404.tpl');

            return;
        }

        $this->smarty->assign('article', $articlePageDTO->article);
        $this->smarty->assign('similarArticles', $articlePageDTO->similarArticles);
        $this->smarty->display('article/show.tpl');
    }
}
