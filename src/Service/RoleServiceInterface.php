<?php

namespace Lmc\Rbac\Mezzio\Service;

use Lmc\Rbac\Service\RoleServiceInterface as BaseRoleServiceInterface;

interface RoleServiceInterface extends BaseRoleServiceInterface
{
    public function matchIdentityRoles(object|null $identity = null, array $roles = []): bool;
}
