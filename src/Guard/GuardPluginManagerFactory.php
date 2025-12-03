<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Psr\Container\ContainerInterface;

final class GuardPluginManagerFactory
{
    public function __invoke(ContainerInterface $container): GuardPluginManager
    {
        /** @var Options $options */
        $options = $container->get(Options::class);
        return new GuardPluginManager(
            $container,
            $options->getGuardManager(),
        );
    }
}
