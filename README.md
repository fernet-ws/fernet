# fernet
A component based PHP Framework

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
        return "<p>Hi <?= $this->name ?>!</p>";
    }
}
```
To use this new component go to the file src/Component/App.php and use it like a custom HTML tag.

```php
<?php declare(strict_types=1);
namespace App\Component;

class App
{
  // There are more code here, let's focus only on the toString method
  public function __toString(): string
  {
    \ob_start(); ?>
    <html lang="en">
        <body>
            <p>Hey check this very original example</p>
            <Hello name="World" />
        </body>
    </html><?php    
    return \ob_get_clean();
  } 
}
```

The functions [ob_start](https://www.php.net/manual/en/function.ob-start.php) and [ob_get_clean](https://www.php.net/manual/en/function.ob-get-clean.php) are used to 
get the printed code. We used this trick when we have a lot of HTML to render. Like many other things used in Fernet this is PHP native.