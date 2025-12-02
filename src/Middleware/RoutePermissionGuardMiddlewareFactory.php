<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuard;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

final class RoutePermissionGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new RoutePermissionsGuardMiddleware(
            $container->get(RoutePermissionGuard::class)
        );
    }
}
