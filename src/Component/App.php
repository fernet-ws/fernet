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
    <title>Fernet</title>
    <link rel="stylesheet" href="/css/styles.css">
    <FernetFavicon />
    <FernetStylesheet />
  </head>
  <body class="welcome">
    <Ribbon>BETA</Ribbon>
    <h1>Fernet</h1>
    <FernetLogo />
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
