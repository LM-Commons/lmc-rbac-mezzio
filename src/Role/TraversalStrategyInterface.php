<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Role;

use Laminas\Permissions\Rbac\RoleInterface;
use Traversable;

interface TraversalStrategyInterface
{
    /**
     * @param Traversable|RoleInterface[] $roles
     */
    public function getRolesIterator(iterable $roles): Traversable;
}
