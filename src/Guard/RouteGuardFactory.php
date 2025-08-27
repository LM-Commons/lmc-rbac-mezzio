<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleService;
use Psr\Container\ContainerInterface;

class RouteGuardFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): GuardInterface
    {
        /** @var Options $options */
        $options = $container->get(Options::class);
        $guards = $options->getGuards();
        $rules = $guards[RouteGuard::class] ?? [];
        return new RouteGuard(
            $container->get(RoleService::class),
            $rules,
            $options->getProtectionPolicy()
        );
    }
}
