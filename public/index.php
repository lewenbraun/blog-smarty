<?php

declare(strict_types=1);

use App\Http\Controllers\HomeController;
use Smarty\Smarty;

require dirname(__DIR__) . '/vendor/autoload.php';

$projectRoot = dirname(__DIR__);

$smarty = new Smarty();
$smarty
    ->setTemplateDir($projectRoot . '/templates')
    ->setCompileDir($projectRoot . '/storage/cache/smarty/compile')
    ->setCacheDir($projectRoot . '/storage/cache/smarty/cache');

$homeController = new HomeController($smarty);
$homeController->index();
