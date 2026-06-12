<?php

namespace Baconfy\Core\Tests\Fixtures\Notes;

use Baconfy\Core\ModuleProvider;
use Baconfy\Core\Navigation;
use Illuminate\Support\Facades\Route;

class NotesServiceProvider extends ModuleProvider
{
    public static int $navigationCalls = 0;

    public static int $labelCalls = 0;

    /**
     * Retrieves the name identifier as a string.
     */
    public function name(): string
    {
        return 'notes';
    }

    /**
     * Retrieves the icon identifier as a string.
     */
    public function icon(): string
    {
        return 'notebook-pen';
    }

    /**
     * Retrieves the label associated with the current context.
     */
    public function label(): string
    {
        static::$labelCalls++;

        return 'Notes';
    }

    /**
     * Retrieves the navigation links as an iterable or null.
     */
    public function navigation(): ?iterable
    {
        static::$navigationCalls++;

        return [
            Navigation::make('All notes')->route('notes.index'),
        ];
    }

    /**
     * Initializes the application routes by applying middleware, prefix, and naming conventions,
     * and loads the route definitions from the specified file.
     */
    public function boot(): void
    {
        Route::middleware('web')
            ->prefix($this->name())
            ->name($this->name().'.')
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));
    }
}
