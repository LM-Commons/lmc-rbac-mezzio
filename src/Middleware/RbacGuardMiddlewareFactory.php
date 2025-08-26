<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Server\MiddlewareInterface;

class RbacGuardMiddlewareFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): MiddlewareInterface
    {
        return new RbacGuardMiddleware(
            $container->get(GuardInterface::class),
        );
    }
}
