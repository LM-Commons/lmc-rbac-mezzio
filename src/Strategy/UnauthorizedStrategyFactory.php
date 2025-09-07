<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Laminas\ServiceManager\Factory\FactoryInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UnauthorizedStrategyFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): UnauthorizedStrategy
    {
        $options = $container->get(Options::class);
        $strategies = $options->getStrategies();
        $options = $strategies[UnauthorizedStrategy::class] ?? [];
        return new UnauthorizedStrategy(
            new UnauthorizedStrategyOptions($options),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
