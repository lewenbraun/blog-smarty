<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;

/** @var HomeController $homeController */
/** @var ArticleController $articleController */

return [
    [
        'method' => 'GET',
        'pattern' => '#^/articles/([^/]+)/?$#',
        'controller' => $articleController,
        'action' => 'show',
    ],
];
