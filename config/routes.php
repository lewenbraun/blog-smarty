<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\HomeController;

/** @var HomeController $homeController */
/** @var ArticleController $articleController */
/** @var CategoryController $categoryController */

return [
    [
        'method' => 'GET',
        'pattern' => '#^/$#',
        'controller' => $homeController,
        'action' => 'index',
    ],
    [
        'method' => 'GET',
        'pattern' => '#^/categories/([^/]+)/?$#',
        'controller' => $categoryController,
        'action' => 'show',
    ],
    [
        'method' => 'GET',
        'pattern' => '#^/articles/([^/]+)/?$#',
        'controller' => $articleController,
        'action' => 'show',
    ],
];
