<?php

declare(strict_types=1);

namespace Baconfy\Core;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Gate;

class Module
{
    /**
     * Constructor method.
     */
    public function __construct(protected string $name, protected int $order = 100, protected ?string $can = null) {}

    /**
     * Retrieves the name.
     */
    public function name(): string
    {
        return $this->name;
    }

    /**
     * Retrieves the order identifier.
     */
    public function order(): int
    {
        return $this->order;
    }

    /**
     * Determines if the current entity is visible for the given user.
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
}
