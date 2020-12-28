<?php

declare(strict_types=1);

namespace Fernet\Component;

use Exception;

class FernetShowError
{
    public Exception $exception;
    public bool $preventWrapper = true;

    public function __toString()
    {
        // TODO Improve error show
        $dump = var_export($this->exception, true);
        return "<html><body><h1>Fernet - Error</h1><pre>$dump</pre></body></html>";
    }
}

