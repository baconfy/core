<?php

namespace Baconfy\Core\Exceptions;

use Exception;

class ManifestMismatchException extends Exception
{
    /**
     * Constructor method for initializing the exception with a specific error message.
     *
     * @return void
     */
    public function __construct(string $provider, string $manifest)
    {
        parent::__construct("Module provider [{$provider}] does not match the module [{$manifest}] declared in composer.json (extra.baconfy.module).");
    }
}
