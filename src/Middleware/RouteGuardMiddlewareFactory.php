<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RouteGuardMiddlewareFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): MiddlewareInterface
    {
        return new RouteGuardMiddleware(
            $container->get(RouteGuard::class),
        );
    }
}
