<?php

declare(strict_types=1);

use App\Http\Controllers\ArticleController;
use App\Http\Controllers\HomeController;
use App\Http\Router;
use App\Repositories\Article\ArticleRepository;
use App\Repositories\Category\CategoryRepository;
use App\Services\Article\ArticleService;
use App\Services\Home\HomeService;
use Doctrine\ORM\EntityManagerInterface;
use Smarty\Smarty;

require dirname(__DIR__) . '/vendor/autoload.php';

$projectRoot = dirname(__DIR__);

$smarty = new Smarty();
$smarty
    ->setTemplateDir($projectRoot . '/templates')
    ->setCompileDir($projectRoot . '/storage/cache/smarty/compile')
    ->setCacheDir($projectRoot . '/storage/cache/smarty/cache')
    ->setEscapeHtml(true);

$entityManager = require $projectRoot . '/config/doctrine.php';

if (!$entityManager instanceof EntityManagerInterface) {
    throw new LogicException('Doctrine configuration must return an entity manager.');
}

$articleRepository = new ArticleRepository($entityManager);
$articleService = new ArticleService($articleRepository);
$articleController = new ArticleController($smarty, $articleService);
$categoryRepository = new CategoryRepository($entityManager);
$homeService = new HomeService($categoryRepository, $articleRepository);
$homeController = new HomeController($smarty, $homeService);

/**
 * @var list<array{
 *     method: string,
 *     pattern: string,
 *     controller: object,
 *     action: string
 * }> $routes
 */
$routes = require $projectRoot . '/config/routes.php';

$router = new Router($routes);

if ($router->dispatch()) {

    return;
}

http_response_code(404);
$smarty->display('errors/404.tpl');
