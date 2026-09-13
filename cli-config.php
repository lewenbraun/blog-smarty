<?php

declare(strict_types=1);

use Doctrine\Migrations\Configuration\EntityManager\ExistingEntityManager;
use Doctrine\Migrations\Configuration\Migration\PhpFile;
use Doctrine\Migrations\DependencyFactory;
use Doctrine\ORM\EntityManagerInterface;

require_once __DIR__ . '/vendor/autoload.php';

/** @var EntityManagerInterface $entityManager */
$entityManager = require __DIR__ . '/config/doctrine.php';

return DependencyFactory::fromEntityManager(
    new PhpFile(__DIR__ . '/config/migrations.php'),
    new ExistingEntityManager($entityManager),
);
