<?php

declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

session_start();

Dotenv\Dotenv::createImmutable(__DIR__)->load();
Fernet\Framework::setUp([
    'rootPath' => __DIR__,
]);
