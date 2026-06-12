<?php

declare(strict_types=1);

namespace Baconfy\Core;

use Closure;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;

class Module
{
    /**
     * Constructor method.
     */
    public function __construct(
        protected string $name,
        protected int $order = 100,
        protected ?string $can = null,
        protected ?string $icon = null,
        protected ?string $routeName = null,
        protected ?Closure $label = null,
        protected ?Closure $navigation = null
    ) {}

    /**
     * Retrieves the name value.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Retrieves the icon value.
     */
    public function icon(): ?string
    {
        return $this->icon;
    }

    /**
     * Retrieves the name of the route.
     */
    public function routeName(): ?string
    {
        return $this->routeName;
    }

    /**
     * Retrieves the label value. If the label is not set, it defaults to the name.
     */
    public function label(): string
    {
        return $this->label === null ? $this->name : ($this->label)();
    }

    /**
     * Retrieves the order identifier.
     */
    public function order(): int
    {
        return $this->order;
    }

    /**
     * Retrieves the navigation data as an iterable structure if available.
     *
     * @return iterable<Navigation>|null
     */
    public function navigation(): ?iterable
    {
        return $this->navigation === null ? null : ($this->navigation)();
    }

    /**
     * Determines if the current entity is visible to the given user.
     */
    public function visibleFor(?Authenticatable $user): bool
    {
        if ($this->can !== null) {
            return $user !== null && Gate::forUser($user)->allows($this->can);
        }

        if (Gate::has($this->name)) {
            return $user !== null && Gate::forUser($user)->allows($this->name);
        }

        return true;
    }

    /**
     * Converts the navigation instance into an array representation.
     *
     * @return array<string, mixed>
     */
    public function toArray(?Authenticatable $user = null): array
    {
        $navigation = $this->navigation();

        return [
            'name' => $this->name,
            'label' => $this->label(),
            'icon' => $this->icon,
            'url' => $this->routeName !== null && Route::has($this->routeName) ? route($this->routeName) : null,
            'order' => $this->order,
            'navigation' => $navigation === null ? null : collect($navigation)
                ->map(fn (Navigation $item) => $item->toArray($user))
                ->filter()
                ->values()
                ->all(),
        ];
    }
}
