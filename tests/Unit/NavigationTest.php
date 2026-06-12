<?php

declare(strict_types=1);

use Baconfy\Core\Navigation;
use Illuminate\Foundation\Auth\User;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

it('serializes label, icon and resolved url', function () {
    Route::get('/notes', fn () => 'ok')->name('notes.index');

    $item = Navigation::make('All notes')->icon('list')->route('notes.index');

    expect($item->toArray())->toBe([
        'label' => 'All notes',
        'icon' => 'list',
        'url' => route('notes.index'),
        'children' => [],
    ]);
});

it('serializes children recursively', function () {
    Route::get('/notes', fn () => 'ok')->name('notes.index');
    Route::get('/notes/archive', fn () => 'ok')->name('notes.archive');

    $item = Navigation::make('Notes')->route('notes.index')->children([
        Navigation::make('Archive')->route('notes.archive')->children([
            Navigation::make('Deep')->route('notes.index'),
        ]),
    ]);

    $result = $item->toArray();

    expect($result['children'][0]['label'])->toBe('Archive')
        ->and($result['children'][0]['children'][0]['label'])->toBe('Deep');
});

it('hides items the user cannot access', function () {
    Route::get('/notes', fn () => 'ok')->name('notes.index');
    Gate::define('see-archive', fn (User $user) => false);

    $item = Navigation::make('Notes')->route('notes.index')->children([
        Navigation::make('Archive')->route('notes.index')->can('see-archive'),
    ]);

    expect($item->toArray(new User)['children'])->toBe([]);
});

it('hides items with can from guests', function () {
    Route::get('/notes', fn () => 'ok')->name('notes.index');

    $item = Navigation::make('Archive')->route('notes.index')->can('see-archive');

    expect($item->toArray(null))->toBeNull();
});

it('prunes routeless groups whose children are all hidden', function () {
    Route::get('/notes', fn () => 'ok')->name('notes.index');
    Gate::define('see-archive', fn (User $user) => false);

    $group = Navigation::make('Website')->children([
        Navigation::make('Archive')->route('notes.index')->can('see-archive'),
    ]);

    expect($group->toArray(new User))->toBeNull();
});
