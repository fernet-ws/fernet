<?php
declare(strict_types=1);

namespace App\Component;

class App
{
    /* For a clean start delete from here... */ 
    private const GREETINGS = ['Hello', 'Hi', 'Howdy', 'Welcome', 'Bounjour', 'Hola'];

    private string $url = \Fernet\Framework::URL;
    private string $greeting;
    private string $name;

    public function __construct(private \Faker\Generator $faker) 
    {
        $this->greeting = static::GREETINGS[array_rand(static::GREETINGS)];
        $this->name = $this->faker->firstName();
    }
    /* ...to this line */

    public function __toString(): string
    {


        ob_start(); ?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Welcome to Fernet</title>
    <link rel="stylesheet" href="/css/styles.css">
    <FernetStylesheet />

    <?php /* For a clean start delete this */ ?>
    <FernetFavicon />
    <?php /* ...to this line */ ?>

  </head>
  <body>

    <?php /* For a clean start delete from here... */ ?>

    <div class="ribbon"><span>Beta</span></div>
    
    <div class="container p-5">
      <div class="row">
        <div class="col-md-2 offset-md-1 d-flex align-items-center mb-4">
          <div class="flex-shrink-0">
            <a href="<?= $this->url ?>" target="_blank">
              <FernetLogo width="100" height="100" />
            </a>
          </div>
          <div class="flex-grow-1 ms-3">
            <h2>
              <a href="<?= $this->url ?>" class="fernet" target="_blank">Fernet</a>
            </h2>
          </div>
        </div>
        <div class="col-md-6 offset-md-1 fs-4">
          <h1 class="mb-5"><?= $this->greeting ?> <?= $this->name ?>!</h1>
          <p>You have successful set up <strong><a href="<?= $this->url ?>" class="fernet" target="_blank">Fernet</a></strong>, the PHP framework based on components.</p>
          <p class="my-5 text-center">
            <a href="subl://<?php echo __FILE__; ?>" class="btn btn-primary">Edit this file</a>
          </p>
          <p>Don't know how to continue from here? Go to the <a href="<?= $this->url ?>" target="_blank" role="button">documentation</a>.</p>
          <p class="fs-6 fst-italic">You are not <?= $this->name ?>? Don't worry <a href=".">try again</a>.</p>
        </div>
      </div>
    </div>
    <script>window.FERNET = <?= json_encode(['URL' => $this->url]) ?>;</script>

    <?php /* ...to this line */ ?>

    <FernetJs />
    <script src="js/app.js"></script>
    <LiveReload />
  </body>
</html><?php
    return ob_get_clean();
    }
}
