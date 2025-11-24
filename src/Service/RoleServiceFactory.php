<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Service;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Role\RecursiveRoleIteratorStrategy;
use Lmc\Rbac\Service\RoleServiceInterface;
use Psr\Container\ContainerInterface;

class RoleServiceFactory
{
    public function __invoke(ContainerInterface $container): RoleService
    {
        /** @var Options $options */
        $options = $container->get(Options::class);

        $traversalStrategy = new RecursiveRoleIteratorStrategy();

        /** @var RoleServiceInterface $rbacRoleService */
        $rbacRoleService = $container->get(RoleServiceInterface::class);

        return new RoleService($rbacRoleService, $traversalStrategy);
    }
}
