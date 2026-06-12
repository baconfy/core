<?php

declare(strict_types=1);

namespace Baconfy\Core;

use Illuminate\Support\ServiceProvider;

class CoreServiceProvider extends ServiceProvider
{
    /**
     * Registers a new entity or service within the system.
     */
    public function register(): void
    {
        $this->app->singleton(ModuleRegistry::class);
    }
}
