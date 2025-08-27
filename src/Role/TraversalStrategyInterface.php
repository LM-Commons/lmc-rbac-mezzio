<?php

namespace Lmc\Rbac\Mezzio\Role;

use Laminas\Permissions\Rbac\RoleInterface;
use Traversable;

interface TraversalStrategyInterface
{
    /**
     * @param Traversable|RoleInterface[] $roles
     * @return Traversable
     */
    public function getRolesIterator(iterable $roles): Traversable;
}
