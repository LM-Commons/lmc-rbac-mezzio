<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Role;

use ArrayIterator;
use Laminas\Permissions\Rbac\RoleInterface;
use RecursiveIterator;
use Traversable;

use function iterator_to_array;

class RecursiveRoleIterator extends ArrayIterator implements RecursiveIterator
{
    /**
     * Override constructor to accept {@link Traversable} as well
     *
     * @param RoleInterface[]|Traversable $roles
     */
    public function __construct(iterable $roles)
    {
        if ($roles instanceof Traversable) {
            $roles = iterator_to_array($roles);
        }

        parent::__construct($roles);
    }

    public function valid(): bool
    {
        return $this->current() instanceof RoleInterface;
    }

    public function hasChildren(): bool
    {
        $current = $this->current();

        if (! $current instanceof RoleInterface) {
            return false;
        }
        return ! empty($current->getChildren());
    }

    public function getChildren(): ?RecursiveRoleIterator
    {
        return new RecursiveRoleIterator($this->current()->getChildren());
    }
}
