<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RouteGuardMiddlewareFactory
{
    public function __invoke(ContainerInterface $container): MiddlewareInterface
    {
        return new RouteGuardMiddleware(
            $container->get(RouteGuard::class)
        );
    }
}
