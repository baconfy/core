<?php

namespace Baconfy\Core\Tests\Fixtures\Notes;

use Baconfy\Core\ModuleProvider;
use Illuminate\Support\Facades\Route;

class NotesServiceProvider extends ModuleProvider
{
    public function name(): string
    {
        return 'notes';
    }

    public function label(): string
    {
        return 'Notes';
    }

    public function icon(): string
    {
        return 'notebook-pen';
    }

    public function boot(): void
    {
        Route::middleware('web')
            ->prefix($this->name())
            ->name($this->name().'.')
            ->group(fn () => $this->loadRoutesFrom(__DIR__.'/../routes/web.php'));
    }
}
