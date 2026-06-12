<p align="center">
    <img src="https://raw.githubusercontent.com/baconfy/core/main/docs/presentation.jpg" width="100%" alt="Prompt">
</p>

[![Tests](https://github.com/baconfy/core/actions/workflows/tests.yml/badge.svg)](https://github.com/baconfy/core/actions/workflows/tests.yml)
[![Latest Version](https://img.shields.io/packagist/v/baconfy/core.svg)](https://packagist.org/packages/baconfy/core)
[![License](https://img.shields.io/packagist/l/baconfy/core.svg)](https://packagist.org/packages/baconfy/core)
[![Total Downloads](https://img.shields.io/packagist/dt/baconfy/core.svg)](https://packagist.org/packages/baconfy/core)
[![PHP Version](https://img.shields.io/packagist/php-v/baconfy/core.svg)](https://packagist.org/packages/baconfy/core)

The module system at the heart of the Baconfy ecosystem. It lets Composer packages register themselves as application modules — with a name, label, icon, entry route, navigation tree and visibility rules — so the application shell can discover and render them with zero configuration.

This package is **pure PHP** and has no opinion about your frontend. It is consumed by [`baconfy/interface`](https://github.com/baconfy/interface), which turns registered modules into a launcher and sidebar.

## Requirements

- PHP 8.3+
- Laravel 11, 12 or 13

## Installation

```bash
composer require baconfy/core
```

The service provider is auto-discovered. A `ModuleRegistry` singleton becomes available in the container.

## Creating a module

A module is a regular Laravel package whose service provider extends `ModuleProvider`. Three methods are required:

```php
use Baconfy\Core\ModuleProvider;

class NotesServiceProvider extends ModuleProvider
{
    public function name(): string
    {
        return 'notes';
    }

    public function label(): string
    {
        return __('Notes');
    }

    public function icon(): string
    {
        return 'notebook-pen';
    }
}
```

That's it. The module is registered automatically when the provider boots.

### Your `boot()` stays yours

`ModuleProvider` does **not** touch the `boot()` method — registration happens through a provider `booting` callback. Write your boot exactly the way you would in any Laravel package:

```php
public function boot(): void
{
    Route::middleware('web')
        ->prefix($this->name())
        ->name($this->name().'.')
        ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));

    $this->loadMigrationsFrom(__DIR__.'/../database/migrations');
}
```

## Conventions

The module **name** is the universal key. By convention it is also:

| Concern | Convention | Override |
| --- | --- | --- |
| Entry route | `{name}.index` | `routeName(): ?string` |
| Visibility gate | gate named `{name}`, if defined | `can(): ?string` |
| Launcher position | `order = 100` | `order(): int` |
| JS page namespace | `extra.baconfy.module` in composer.json | — (must match `name()`) |

### Visibility

Three rules, checked in order:

1. **Explicit `can()`** — the module is visible only to users allowed by that gate.
2. **Convention gate** — if a gate named after the module exists (`Gate::define('notes', ...)`), it decides. The application controls access without the module knowing.
3. **Otherwise** — the module is public, including for guests.

### Manifest validation

If the package's `composer.json` declares a Baconfy manifest, the module key must match the provider's `name()`:

```json
{
    "extra": {
        "baconfy": {
            "module": "notes"
        }
    }
}
```

A mismatch throws `ManifestMismatchException` at boot — configuration errors die early and loudly. The manifest is optional: headless modules (no frontend pages) don't need one.

## Navigation

Modules may expose a navigation tree by overriding `navigation()`:

```php
use Baconfy\Core\Navigation;

public function navigation(): ?iterable
{
    return [
        Navigation::make(__('All notes'))->icon('list')->route('notes.index'),

        Navigation::make(__('Archive'))->children(
            Note::distinctYears()->map(
                fn (int $year) => Navigation::make((string) $year)
                    ->route('notes.archive', ['year' => $year]),
            ),
        ),

        Navigation::make(__('Settings'))
            ->route('notes.settings')
            ->can('notes.settings'),
    ];
}
```

- Trees nest to any depth through `children()` (accepts arrays or Collections).
- Items with `can()` are pruned for users who fail the gate — and for guests.
- Route-less items act as group headings; a group whose children were all pruned disappears with them.
- Returning `null` (the default) means the module has no sidebar navigation at all.

## Lazy by design

`label()` and `navigation()` are **never called during boot**. They are wrapped in closures and evaluated only at serialization time, per request. This means:

- labels translate to the locale of the current request, not the boot locale;
- navigation may safely query the database without taxing every request;
- long-running runtimes (Octane) never freeze stale values.

## Consuming the registry

```php
use Baconfy\Core\ModuleRegistry;

$registry = app(ModuleRegistry::class);

$registry->all();                       // every module, sorted by order then name
$registry->visibleFor($user);           // only what this user may see
$registry->get('notes');                // a single module
```

Sharing modules with an Inertia frontend is a one-liner:

```php
Inertia::share('baconfy.modules', fn () => app(ModuleRegistry::class)
    ->visibleFor(auth()->user())
    ->map->toArray(auth()->user()));
```

Each module serializes to:

```json
{
    "name": "notes",
    "label": "Notes",
    "icon": "notebook-pen",
    "url": "https://app.test/notes",
    "order": 100,
    "navigation": [
        { "label": "All notes", "icon": "list", "url": "https://app.test/notes", "children": [] }
    ]
}
```

`url` is `null` when the entry route doesn't exist; `navigation` is `null` when the module defines none.

## Testing

```bash
composer test   # pest
composer lint   # pint + phpstan
```

The suite runs against PHP 8.3/8.4/8.5 × Laravel 11/12/13 in CI.

## License

MIT.