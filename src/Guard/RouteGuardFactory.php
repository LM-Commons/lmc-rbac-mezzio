<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

use function assert;
use function is_array;

class RouteGuardFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GuardInterface
    {
        /** @var Options $moduleOptions */
        $moduleOptions = $container->get(Options::class);
        $rules         = $moduleOptions->getGuardOptions(RouteGuard::class);
        assert(is_array($rules));

        /** @psalm-suppress MixedArgument */
        return new RouteGuard(
            $container->get(RoleServiceInterface::class),
            $rules,
            $moduleOptions->getProtectionPolicy()
        );
    }
}
