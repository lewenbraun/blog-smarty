<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Smarty\Smarty;

class HomeController
{
    public function __construct(private Smarty $smarty) {}

    public function index(): void
    {
        $this->smarty->display('home.tpl');
    }
}
