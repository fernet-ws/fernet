<?php

declare(strict_types=1);
require __DIR__.'/vendor/autoload.php';

Dotenv\Dotenv::createImmutable(__DIR__)->load();
Fernet\Framework::setUp([
    'rootPath' => __DIR__,
]);

$faker = \Faker\Factory::create($_ENV['FAKER_LANG']);
$faker->seed($_ENV['FAKER_SEED']);
Fernet\Framework::getInstance()->getContainer()->add($faker::class, $faker);
