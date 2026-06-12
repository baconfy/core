<?php

declare(strict_types=1);

use Baconfy\Core\Exceptions\DuplicateModuleException;
use Baconfy\Core\Module;
use Baconfy\Core\ModuleRegistry;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;

it('registers and retrieves modules by name', function () {
    $registry = new ModuleRegistry;
    $module = new Module(name: 'notes');

    $registry->register($module);

    expect($registry->get('notes'))->toBe($module)
        ->and($registry->has('notes'))->toBeTrue();
});

it('throws when registering a duplicate module name', function () {
    $registry = new ModuleRegistry;

    $registry->register(new Module(name: 'notes'));

    expect(fn () => $registry->register(new Module(name: 'notes')))
        ->toThrow(DuplicateModuleException::class, 'notes');
});

it('orders modules by order then name', function () {
    $registry = new ModuleRegistry;

    $registry->register(new Module(name: 'zebra'));
    $registry->register(new Module(name: 'alpha'));
    $registry->register(new Module(name: 'beta', order: 50));

    expect($registry->all()->map->name()->all())
        ->toBe(['beta', 'alpha', 'zebra']);
});

it('shows modules without gate to everyone', function () {
    $registry = new ModuleRegistry;

    $registry->register(new Module(name: 'notes'));

    expect($registry->visibleFor(null))->toHaveCount(1)
        ->and($registry->visibleFor(new User))->toHaveCount(1);
});

it('uses the gate named after the module when defined', function () {
    Gate::define('admin', fn (User $user) => false);
    Gate::define('reports', fn (User $user) => true);

    $registry = new ModuleRegistry;

    $registry->register(new Module(name: 'admin'));
    $registry->register(new Module(name: 'reports'));

    expect($registry->visibleFor(new User)->map->name()->all())->toBe(['reports'])
        ->and($registry->visibleFor(null))->toHaveCount(0);
});

it('prefers an explicit can over the convention gate', function () {
    Gate::define('admin', fn (User $user) => false);
    Gate::define('manage-settings', fn (User $user) => true);

    $registry = new ModuleRegistry;

    $registry->register(new Module(name: 'admin', can: 'manage-settings'));

    expect($registry->visibleFor(new User))->toHaveCount(1)
        ->and($registry->visibleFor(null))->toHaveCount(0);
});
