<?php

declare(strict_types=1);

namespace Baconfy\Core;

use Baconfy\Core\Exceptions\DuplicateModuleException;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Collection;

class ModuleRegistry
{
    /** @var array<string, Module> */
    protected array $modules = [];

    /**
     * Registers a module by adding it to the module's collection.
     *
     * @throws DuplicateModuleException
     */
    public function register(Module $module): void
    {
        if ($this->has($module->name())) {
            throw new DuplicateModuleException($module->name());
        }

        $this->modules[$module->name()] = $module;
    }

    /**
     * Retrieves all modules, sorted by their order and name, and returns them as a collection.
     *
     * @return Collection<int, Module>
     */
    public function all(): Collection
    {
        return collect($this->modules)->sortBy(fn (Module $module) => [$module->order(), $module->name()])->values();
    }

    /**
     * Retrieves a module by its name.
     */
    public function get(string $name): ?Module
    {
        return $this->modules[$name] ?? null;
    }

    /**
     * Checks if a module exists by its name.
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->modules);
    }

    /**
     * Determines if the module is visible for the given user.
     *
     * @return Collection<int, Module>
     */
    public function visibleFor(?Authenticatable $user): Collection
    {
        return $this->all()->filter(fn (Module $module) => $module->visibleFor($user))->values();
    }
}
