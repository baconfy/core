<?php

declare(strict_types=1);

namespace Baconfy\Core\Exceptions;

use Exception;

class DuplicateModuleException extends Exception
{
    public function __construct(string $name)
    {
        parent::__construct("Module [{$name}] is already registered. Module names must be unique across the application.");
    }
}
