<?php

declare(strict_types=1);

namespace Tests;

use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use LogicException;
use PHPUnit\Framework\TestCase;

abstract class IntegrationTestCase extends TestCase
{
    private ?EntityManagerInterface $entityManager = null;

    protected function setUp(): void
    {
        parent::setUp();

        $ormConfiguration = ORMSetup::createAttributeMetadataConfig(
            paths: [dirname(__DIR__) . '/src/Models'],
            isDevMode: true,
        );
        $ormConfiguration->enableNativeLazyObjects(true);

        $connection = DriverManager::getConnection(
            [
                'driver' => 'pdo_sqlite',
                'memory' => true,
            ],
            $ormConfiguration,
        );

        $this->entityManager = new EntityManager(
            $connection,
            $ormConfiguration,
        );

        $metadata = $this->entityManager
            ->getMetadataFactory()
            ->getAllMetadata();

        new SchemaTool($this->entityManager)->createSchema($metadata);
    }

    protected function tearDown(): void
    {
        $this->entityManager?->close();
        $this->entityManager = null;

        parent::tearDown();
    }

    protected function entityManager(): EntityManagerInterface
    {
        if ($this->entityManager === null) {
            throw new LogicException('The test entity manager has not been initialized.');
        }

        return $this->entityManager;
    }
}
