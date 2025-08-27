<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Service;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Role\RecursiveRoleIteratorStrategy;
use Lmc\Rbac\Service\RoleServiceInterface;
use Psr\Container\ContainerInterface;

class RoleServiceFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RoleService
    {
        /** @var Options $options */
        $options = $container->get(Options::class);

        $traversalStrategy = new RecursiveRoleIteratorStrategy();

        $rbacRoleService = $container->get(RoleServiceInterface::class);

        return new RoleService($rbacRoleService, $traversalStrategy);
    }
}
