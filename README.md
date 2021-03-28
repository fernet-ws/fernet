<h1 align="center">Fernet</h1>
<p align="center">
    <a href="https://packagist.org/packages/fernet/fernet">
        <img src="https://img.shields.io/packagist/v/fernet/fernet" alt="Latest Stable Version">
    </a>
    <a href="https://www.travis-ci.com/pragmore/fernet-core">
        <img src="https://www.travis-ci.com/pragmore/fernet-core.svg?branch=main" alt="Build Status">
    </a>
    <a href='https://coveralls.io/github/pragmore/fernet-core?branch=main'>
        <img src='https://coveralls.io/repos/github/pragmore/fernet-core/badge.svg?branch=main' alt='Coverage Status' />
    </a>
    <a href='https://fernet.readthedocs.io/en/latest/?badge=latest'>
        <img src='https://readthedocs.org/projects/fernet/badge/?version=latest' alt='Documentation Status' />
    </a>
</p>

A component based PHP Framework.

[Read the full documentation](https://fernet.readthedocs.io).

## Set up

To install use [composer](https://getcomposer.org) create project command:

    composer create-project fernet/fernet /path/to/app

You can use the [php built in server](https://www.php.net/manual/en/features.commandline.webserver.php) to run the app:

    php -S 127.0.0.1:14567 -t public

Then go to [127.0.0.1:14567](http://127.0.0.1:14567).

## Component

Fernet component are inspired by React component. They are a PHP class with a [__toString](https://www.php.net/manual/en/language.oop5.magic.php#object.tostring) method
that returns the HTML the component will render. The class needs to be created in the **src/Component/**
folder. The namespace should be **App\Component**. Let's create a simple component
that said Hi.

**src/Component/Hello.php**
```php
<?php declare(strict_types=1);
namespace App\Component;

class Hello
{
    public string $name;

    public function __toString(): string
    {
        return "<p>Hi {$this->name}!</p>";
    }
}
```
To use this new component go to the file **src/Component/App.php** and use it like a custom HTML tag.

```php
  // There are more code here, let's focus only on the toString method
  public function __toString(): string
  {
    \ob_start(); ?>
    <html lang="en">
        <body>
            <p>Check out this very original example</p>
            <Hello name="World" />
        </body>
    </html><?php    
    return \ob_get_clean();
  } 
```

The functions [ob_start](https://www.php.net/manual/en/function.ob-start.php) and [ob_get_clean](https://www.php.net/manual/en/function.ob-get-clean.php) are used to 
get the printed code. We used this trick when we have a lot of HTML to render. Like many other things used in Fernet this is PHP native.
The rest of course is old plain HTML.
