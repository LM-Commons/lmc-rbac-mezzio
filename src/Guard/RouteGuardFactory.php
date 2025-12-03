<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class RouteGuardFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @psalm-suppress PossiblyUnusedParam
     */
    public function __invoke(ContainerInterface $container, string $resolvedName, array $options): GuardInterface
    {
        /** @var Options $moduleOptions */
        $moduleOptions = $container->get(Options::class);
        return new RouteGuard(
            /** @psalm-suppress MixedArgument */
            $container->get(RoleServiceInterface::class),
            $options,
            $moduleOptions->getProtectionPolicy()
        );
    }
}
