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
