<?php

namespace Baconfy\Core;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

abstract class ModuleProvider extends ServiceProvider
{
    protected ?string $packageBasePath = null;

    /**
     * Retrieves the name as a string.
     */
    abstract public function name(): string;

    /**
     * Retrieves the label as a string.
     */
    abstract public function label(): string;

    /**
     * Retrieves the icon representation as a string.
     */
    abstract public function icon(): string;

    /**
     * Class constructor.
     *
     * @param  mixed  $app
     * @return void
     */
    public function __construct($app)
    {
        parent::__construct($app);

        $this->booting(function () {
            $this->app->make(ModuleRegistry::class)->register(
                new Module(
                    name: $this->name(),
                    order: $this->order(),
                    can: $this->can(),
                    icon: $this->icon(),
                    routeName: $this->routeName(),
                    label: fn () => $this->label(),
                    navigation: fn () => $this->navigation(),
                ),
            );
        });
    }

    /**
     * Determines if a specific condition or capability is met.
     */
    public function can(): ?string
    {
        return null;
    }

    /**
     * Retrieves the order value.
     */
    public function order(): int
    {
        return 100;
    }

    /**
     * Generates and returns the route name.
     */
    public function routeName(): ?string
    {
        return $this->name().'.index';
    }

    /**
     * Retrieves the navigation items.
     *
     * @return iterable<Navigation>|null
     */
    public function navigation(): ?iterable
    {
        return null;
    }

    /**
     * Determines and returns the base path of the package.
     *
     * The method resolves the base path by searching for a `composer.json` file
     * in the directory hierarchy up to four levels above the directory of the
     * current class file. If found, it caches and returns the directory path.
     * If the path cannot be determined, it returns null.
     */
    protected function packageBasePath(): ?string
    {
        if ($this->packageBasePath !== null) {
            return $this->packageBasePath;
        }

        $fileName = (new ReflectionClass($this))->getFileName();
        if ($fileName === false) {
            return null;
        }

        $dir = dirname($fileName);

        for ($i = 0; $i < 4; $i++) {
            if (file_exists($dir.'/composer.json')) {
                return $this->packageBasePath = $dir;
            }

            $dir = dirname($dir);
        }

        return null;
    }
}
