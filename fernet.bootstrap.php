<?php

declare(strict_types=1);
require __DIR__ . '/vendor/autoload.php';

session_start();

Dotenv\Dotenv::createImmutable(__DIR__)->load();
Fernet\Framework::setUp([
    'rootPath' => __DIR__,
]);

use ParagonIE\AntiCSRF\AntiCSRF;
use Fernet\Framework;

Framework::subscribe('onLoad', function (Framework $framework) {
    $antiServer = $_SERVER;
    // This is the only way to prevent path validation
    $antiServer['REQUEST_URI'] = '/';
    $antiCsrf = new AntiCSRF($_POST, $_SESSION, $antiServer);
    $framework->getContainer()->add(AntiCSRF::class, $antiCsrf);
});
