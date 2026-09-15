<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\Home\Contracts\HomeServiceInterface;
use Smarty\Smarty;

final readonly class HomeController
{
    public function __construct(
        private Smarty $smarty,
        private HomeServiceInterface $homeService,
    ) {}

    public function index(): void
    {
        $this->smarty->assign('categoryArticles', $this->homeService->getHomePage());
        $this->smarty->display('home.tpl');
    }
}
