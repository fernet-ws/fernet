<?php
declare(strict_types=1);

namespace App\Component;

class App
{
    /* Component configuration */
    public bool $preventWrapper = true;

    public function __toString(): string
    {
        ob_start(); ?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/styles.css">
    <title>Fernet</title>
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
  </head>
  <body>
    <div class="ribbon"><span>BETA</span></div>
    <h1>Fernet</h1>
    <img src="logo.png" alt="fernet framework logo" />
    <div class="main">
        <p>Congrats! You have successful installed <strong>Fernet</strong>, the component based php framework.</p>
        <h3>You can write your <em>components</em> now</h3>
        <p>Don't know what <em>components</em> are? Read the <a href="https://github.com/pragmore/fernet" target="_blank" role="button">documentation</a>.</p>
        <p>You can also start <a href="subl://<?php echo __FILE__; ?>">editing this file</a>.</p>
    </div>
    <FernetClientScript />
  </body>
</html><?php
    return ob_get_clean();
    }
}
