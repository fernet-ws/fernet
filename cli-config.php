<?php
require_once __DIR__ . '/bootstrap.php';

use Doctrine\ORM\Tools\Console\ConsoleRunner;
use Doctrine\ORM\EntityManager;
use Fernet\Framework;

$entityManager = Framework::getInstance()->getContainer()->get(EntityManager::class);
ConsoleRunner::createHelperSet($entityManager);
