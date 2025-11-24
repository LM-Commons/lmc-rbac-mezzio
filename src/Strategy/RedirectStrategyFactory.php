<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RedirectStrategyFactory
{
    public function __invoke(ContainerInterface $container): RedirectStrategy
    {
        /** @var Options $options */
        $options    = $container->get(Options::class);
        $strategies = $options->getStrategies();
        $options    = $strategies[RedirectStrategy::class] ?? [];
        return new RedirectStrategy(
            new RedirectStrategyOptions($options),
            $container->get(RouterInterface::class),
            $container->get(ResponseFactoryInterface::class)
        );
    }
}
