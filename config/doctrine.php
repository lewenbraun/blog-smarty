<?php

declare(strict_types=1);

use App\Database\DatabaseConfiguration;
use App\Database\EntityManagerFactory;

$environment = getenv('APP_ENV');

return new EntityManagerFactory(
    databaseConfiguration: DatabaseConfiguration::fromEnvironment(),
    modelsPath: dirname(__DIR__) . '/src/Models',
    developmentMode: $environment === false || $environment !== 'production',
)->create();
