<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Assets;

use Iterator;
use Override;

use function count;

final class TestTraversableRoles implements Iterator
{
    protected array $roles;

    protected int $index = 0;

    public function __construct(array $roles)
    {
        $this->roles = $roles;
    }

    #[Override]
    public function current(): mixed
    {
        return $this->roles[$this->index];
    }

    #[Override]
    public function next(): void
    {
        $this->index++;
    }

    #[Override]
    public function key(): mixed
    {
        return $this->index;
    }

    #[Override]
    public function valid(): bool
    {
        return $this->index < count($this->roles);
    }

    #[Override]
    public function rewind(): void
    {
        $this->index = 0;
    }
}
