<?php

namespace Baconfy\Core;

use Illuminate\Support\ServiceProvider;
use ReflectionClass;

abstract class ModuleProvider extends ServiceProvider
{
    protected ?string $packageBasePath = null;

    abstract public function name(): string;

    abstract public function label(): string;

    abstract public function icon(): string;

    public function __construct($app)
    {
        parent::__construct($app);

        $this->booting(function () {
            $this->app->make(ModuleRegistry::class)->register(
                new Module(name: $this->name()),
            );
        });
    }

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
