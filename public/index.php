<?php

declare(strict_types=1);

require __DIR__ . '/../fernet.bootstrap.php';

use Fernet\Framework;
use App\Component\App;

Framework::getInstance()->run(App::class)->send();
