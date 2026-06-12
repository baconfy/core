<?php

declare(strict_types=1);

use Baconfy\Core\ModuleRegistry;
use Baconfy\Core\Tests\Fixtures\Notes\NotesServiceProvider;
use Illuminate\Support\Facades\Route;

it('registers the module on boot', function () {
    $this->registerModule(NotesServiceProvider::class);

    expect(app(ModuleRegistry::class)->has('notes'))->toBeTrue();
});

it('loads package routes with module prefix and name', function () {
    $this->registerModule(NotesServiceProvider::class);

    expect(Route::has('notes.index'))->toBeTrue();

    $this->get('/notes')->assertOk()->assertSee('notes index');
});

it('does not evaluate navigation during boot', function () {
    NotesServiceProvider::$navigationCalls = 0;

    $this->registerModule(NotesServiceProvider::class);

    expect(NotesServiceProvider::$navigationCalls)->toBe(0);
});

it('evaluates the label lazily at serialization time', function () {
    NotesServiceProvider::$labelCalls = 0;

    $this->registerModule(NotesServiceProvider::class);

    expect(NotesServiceProvider::$labelCalls)->toBe(0);

    $module = app(ModuleRegistry::class)->get('notes');

    expect($module->label())->toBe('Notes')
        ->and(NotesServiceProvider::$labelCalls)->toBe(1);
});

it('serializes the module for the frontend', function () {
    $this->registerModule(NotesServiceProvider::class);

    $module = app(ModuleRegistry::class)->get('notes');

    expect($module->toArray())->toBe([
        'name' => 'notes',
        'label' => 'Notes',
        'icon' => 'notebook-pen',
        'url' => route('notes.index'),
        'order' => 100,
        'navigation' => [
            [
                'label' => 'All notes',
                'icon' => null,
                'url' => route('notes.index'),
                'children' => [],
            ],
        ],
    ]);
});
