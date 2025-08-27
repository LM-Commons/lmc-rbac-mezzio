<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Role;

use Laminas\Permissions\Rbac\RoleInterface;
use RecursiveIteratorIterator;
use Traversable;

/**
 * Create a {@link RecursiveRoleIterator} and wrap it into a {@link RecursiveIteratorIterator}
 */
class RecursiveRoleIteratorStrategy implements TraversalStrategyInterface
{
    /**
     * @param RoleInterface[]|Traversable $roles
     * @return RecursiveIteratorIterator
     */
    public function getRolesIterator(iterable $roles): Traversable
    {
        return new RecursiveIteratorIterator(
            new RecursiveRoleIterator($roles),
            RecursiveIteratorIterator::SELF_FIRST
        );
    }
}
