<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\GuardsFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class GuardMiddlewareFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): GuardMiddleware
    {
        $guardsFactory = new GuardsFactory();
        $guards        = $guardsFactory($container);
        /** @var Options $options */
        $options = $container->get(Options::class);
        return new GuardMiddleware($options, $guards);
    }
}
