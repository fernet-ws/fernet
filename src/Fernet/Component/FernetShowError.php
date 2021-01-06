<?php

declare(strict_types=1);

namespace Fernet\Component;

use Throwable;

class FernetShowError
{
    public Throwable $error;
    public bool $preventWrapper = true;

    public function __toString(): string
    {
        // TODO Improve error show
        $dump = var_export($this->error, true);

        return "<html lang=\"en\"><body><h1>Fernet - Error</h1><pre>$dump</pre></body></html>";
    }
}
