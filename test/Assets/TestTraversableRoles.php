<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Assets;

use Laminas\Permissions\Rbac\Role;

class TestTraversableRoles implements \Iterator
{

    protected array $roles;

    protected int $index = 0;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    public function current(): mixed
    {
        return $this->roles[$this->index];
    }

    public function next(): void
    {
        $this->index++;
    }

    public function key(): mixed
    {
        return $this->index;
    }

    public function valid(): bool
    {
        return $this->index < count($this->roles);
    }

    public function rewind(): void
    {
        $this->index = 0;
    }
}
