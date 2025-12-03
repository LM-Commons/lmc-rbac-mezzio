<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Options\Options;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class GuardsFactory
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function __invoke(ContainerInterface $container): array
    {
        /** @var Options $options */
        $options      = $container->get(Options::class);
        $guardOptions = $options->getGuards();

        if (empty($guardOptions)) {
            return [];
        }

        /** @var GuardPluginManager $guardPluginManager */
        $guardPluginManager = $container->get(GuardPluginManager::class);
        $guards             = [];

        /**
         * @var string $guard
         * @var array $options
         */
        foreach ($guardOptions as $guard => $options) {
            /** @psalm-suppress MixedAssignment */
            $guards[] = $guardPluginManager->get($guard, $options);
        }

        return $guards;
    }
}
