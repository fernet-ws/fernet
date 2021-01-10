<?php

declare(strict_types=1);

namespace App\Component;

use ParagonIE\AntiCSRF\AntiCSRF;

class CsrfToken
{
    private AntiCSRF $csrf;

    public function __construct(AntiCSRF $csrf)
    {
        $this->csrf = $csrf;
    }

    public function __toString(): string
    {
        return $this->csrf->insertToken('', false);
    }
}
