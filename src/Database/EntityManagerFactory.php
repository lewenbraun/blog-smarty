<?php

declare(strict_types=1);

namespace App\Database;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;

final readonly class EntityManagerFactory
{
    public function __construct(
        private DatabaseConfiguration $databaseConfiguration,
        private string $modelsPath,
        private bool $developmentMode,
    ) {}

    public function create(): EntityManagerInterface
    {
        $ormConfiguration = ORMSetup::createAttributeMetadataConfig(
            paths: [$this->modelsPath],
            isDevMode: $this->developmentMode,
        );
        $ormConfiguration->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection([
            'driver' => 'pdo_mysql',
            'host' => $this->databaseConfiguration->host,
            'port' => $this->databaseConfiguration->port,
            'dbname' => $this->databaseConfiguration->database,
            'user' => $this->databaseConfiguration->username,
            'password' => $this->databaseConfiguration->password,
            'charset' => 'utf8mb4',
            'serverVersion' => '8.4.0',
        ], $ormConfiguration);
        return new EntityManager($connection, $ormConfiguration);
    }
}
