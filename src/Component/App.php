<?php
declare(strict_types=1);

namespace App\Component;

use App\Entity\Book;
use Fernet\Params;
use Monolog\Logger;

class App
{
    /* Component configuration */
    public bool $preventWrapper = true;

    private Book $book;
    private Logger $log;
    private string $title;

    public function __construct(Logger $log)
    {
        $this->log = $log;
        $this->setBook(new Book());
    }

    public function setBook(Book $book): self
    {
        $this->log->info("Set current book", [$book]);
        $this->book = $book;
        $this->title = $book->title ? "{$book->title} - " : '';
        $this->title .= $_ENV['TITLE'];

        return $this;
    }

    public function __toString(): string
    {
        ob_start(); ?><!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
    <link rel="stylesheet" href="/css/styles.css">
    <title><?= $this->title ?></title>

    <!-- favicon generated with https://realfavicongenerator.net -->
    <link rel="apple-touch-icon" sizes="120x120" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

  </head>
  <body>
    <div class="ribbon"><span>BETA</span></div>
    <div class="container">
      <section class="row">
        <div class="col">
          <h1 class="text-center"><a href="/"><?= $this->title ?></a></h1>
        </div>
      </section>
      <section class="row">
        <div class="col">
          <BookTable />
        </div>
        <div class="col">
          <BookForm <?= Params::component(['book' => $this->book]) ?> />
        </div>
      </section>
    </div><!-- end container -->
    <FernetClientScript />
  </body>
</html><?php
    return ob_get_clean();
    }
}
