<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Service;

use Laminas\Permissions\Rbac\RoleInterface;
use Lmc\Rbac\Mezzio\Role\TraversalStrategyInterface;
use Lmc\Rbac\Service\RoleServiceInterface as RbacRoleServiceInterface;

use function array_intersect;
use function array_unique;
use function count;

final class RoleService implements RoleServiceInterface
{
    public function __construct(
        private readonly RbacRoleServiceInterface   $rbacRoleService,
        private readonly TraversalStrategyInterface $traversalStrategy,
    ) {
    }

    #[\Override]
    public function getIdentityRoles(object|null $identity = null): array
    {
        return $this->rbacRoleService->getIdentityRoles($identity);
    }

    public function matchIdentityRoles(object|null $identity = null, array $roles = []): bool
    {
        $identityRoles = $this->getIdentityRoles($identity);

        if (empty($identityRoles)) {
            return false;
        }

        $roleNames = [];

        foreach ($roles as $role) {
            $roleNames[] = $role instanceof RoleInterface ? $role->getName() : (string) $role;
        }

        $identityRoles = $this->flattenRoles($identityRoles);

        return count(array_intersect($roleNames, $identityRoles)) > 0;
    }

    /**
     * @param array|RoleInterface[] $roles
     * @return string[]
     */
    protected function flattenRoles(array $roles): array
    {
        $roleNames = [];
        $iterator  = $this->traversalStrategy->getRolesIterator($roles);
        foreach ($iterator as $role) {
            $roleNames[] = $role instanceof RoleInterface ? $role->getName() : (string) $role;
        }
        return array_unique($roleNames);
    }
}
