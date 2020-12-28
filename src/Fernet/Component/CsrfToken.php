<?php

declare(strict_types=1);

namespace Fernet\Component;

use ParagonIE\AntiCSRF\AntiCSRF;

class CsrfToken
{
    private $csrf;

    public function __construct(AntiCSRF $csrf)
    {
        $this->csrf  = $csrf;
    }

    public function __toString()
    {
        return $this->csrf->insertToken('', false);
    }
}
