<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Container\ContainerInterface;

class UnauthorizedStrategyFactory
{
    public function __invoke(ContainerInterface $container): UnauthorizedStrategy
    {
        /** @var Options $options */
        $options    = $container->get(Options::class);
        $strategies = $options->getStrategies();
        $options    = $strategies[UnauthorizedStrategy::class] ?? [];
        return new UnauthorizedStrategy(
            new UnauthorizedStrategyOptions($options),
            $container->get(TemplateRendererInterface::class)
        );
    }
}
