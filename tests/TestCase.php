<?php

namespace Baconfy\Core\Tests;

use Baconfy\Core\CoreServiceProvider;
use Illuminate\Support\Facades\Route;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Random\RandomException;

abstract class TestCase extends BaseTestCase
{
    /**
     * Configures the application environment by setting the encryption key.
     *
     * @param  mixed  $app
     *
     * @throws RandomException
     */
    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:'.base64_encode(random_bytes(32)));
    }

    /**
     * Retrieves a list of package service providers for the application.
     *
     * @param  mixed  $app
     */
    protected function getPackageProviders($app): array
    {
        return [CoreServiceProvider::class];
    }

    /**
     * Registers a module provider within the application and refreshes route name lookups.
     *
     * @param  string  $provider  The service provider class to be registered.
     */
    protected function registerModule(string $provider): void
    {
        $this->app->register($provider);

        Route::getRoutes()->refreshNameLookups();
    }
}
