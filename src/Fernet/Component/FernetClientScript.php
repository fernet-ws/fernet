<?php

declare(strict_types=1);

namespace Fernet\Component;

class FernetClientScript
{
    public bool $preventWrapper = true;

    public function __toString()
    {
        return '<script defer src="/js/fernet.js"></script>';
    }
}
