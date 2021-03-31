<?php

$file = __DIR__ . '/public' . $_SERVER["REQUEST_URI"];
if (file_exists($file) && is_file($file)) {
    return false;
}

require 'public/index.php';
