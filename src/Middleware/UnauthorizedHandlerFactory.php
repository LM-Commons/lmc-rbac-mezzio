<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Options\Options;
use Psr\Container\ContainerInterface;

final class UnauthorizedHandlerFactory
{
    public function __invoke(ContainerInterface $container): UnauthorizedHandler
    {
        return new UnauthorizedHandler(
            $container->get(Options::class),
        );
    }
}
