<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Mezzio\Router\RouterInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

class RedirectStrategyFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): RedirectStrategy
    {
        $options = $container->get(Options::class);
        $strategies = $options->getStrategies();
        $options = $strategies[RedirectStrategy::class] ?? [];
        return new RedirectStrategy(
            new RedirectStrategyOptions($options),
            $container->get(RouterInterface::class),
            $container->get(ResponseFactoryInterface::class)

        );
    }
}
