<?php

declare(strict_types=1);

namespace Baconfy\Core;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class Navigation
{
    protected ?string $icon = null;

    protected ?string $routeName = null;

    protected ?string $can = null;

    /** @var array<string, mixed> */
    protected array $routeParams = [];

    /** @var array<int, Navigation> */
    protected array $children = [];

    /**
     * Constructor method to initialize the object with a label.
     *
     * @return void
     */
    public function __construct(protected string $label) {}

    /**
     * Creates a new instance with the specified label.
     */
    public static function make(string $label): self
    {
        return new self($label);
    }

    /**
     * Sets the icon for the instance.
     */
    public function icon(string $icon): self
    {
        $this->icon = $icon;

        return $this;
    }

    /**
     * Defines a route with a specified name and parameters.
     *
     * @param  array<string, mixed>  $params
     */
    public function route(string $name, array $params = []): self
    {
        $this->routeName = $name;
        $this->routeParams = $params;

        return $this;
    }

    /**
     * Sets the children elements.
     *
     * @param  iterable<int, Navigation>  $children
     */
    public function children(iterable $children): self
    {
        $this->children = collect($children)->all();

        return $this;
    }

    /**
     * Sets a specific ability for the instance.
     */
    public function can(string $ability): self
    {
        $this->can = $ability;

        return $this;
    }

    /**
     * Converts the current object into an associative array representation.
     *
     * @return array<string, mixed>|null
     */
    public function toArray(?Authenticatable $user = null): ?array
    {
        if ($this->can !== null) {
            if ($user === null || ! Gate::forUser($user)->allows($this->can)) {
                return null;
            }
        }

        $children = collect($this->children)->map(fn (Navigation $child) => $child->toArray($user))->filter()->values()->all();

        if ($this->routeName === null && $children === []) {
            return null;
        }

        return [
            'label' => $this->label,
            'icon' => $this->icon,
            'url' => $this->routeName ? route($this->routeName, $this->routeParams) : null,
            'children' => $children,
        ];
    }
}
