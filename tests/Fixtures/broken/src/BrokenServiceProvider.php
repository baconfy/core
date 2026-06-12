<?php

declare(strict_types=1);

namespace Baconfy\Core\Tests\Fixtures\Broken;

use Baconfy\Core\ModuleProvider;

class BrokenServiceProvider extends ModuleProvider
{
    public function name(): string
    {
        return 'broken';
    }

    public function label(): string
    {
        return 'Broken';
    }

    public function icon(): string
    {
        return 'bug';
    }
}
