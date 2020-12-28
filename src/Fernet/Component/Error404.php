<?php
declare(strict_types=1);
namespace Fernet\Component;

class Error404
{
    public bool $preventWrapper = true;

    public function __toString()
    {
        return '<html><body><h1>Error 404</h1><p>Page not found</p></body></html>';
    }
}
