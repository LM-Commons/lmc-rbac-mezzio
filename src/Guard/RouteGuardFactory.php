<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Psr\Container\ContainerInterface;

class RouteGuardFactory
{
    public function __invoke(ContainerInterface $container): GuardInterface
    {
        /** @var Options $options */
        $options = $container->get(Options::class);
        $guards  = $options->getGuards();
        /** @var array $rules */
        $rules = $guards[RouteGuard::class] ?? [];
        return new RouteGuard(
            $container->get(RoleServiceInterface::class),
            $rules,
            $options->getProtectionPolicy()
        );
    }
}
