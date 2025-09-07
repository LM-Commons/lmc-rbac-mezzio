<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\DelegatorFactoryInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use Psr\Container\ContainerInterface;

class GuardMiddlewareDelegatorFactory implements DelegatorFactoryInterface
{
    public function __invoke(
        ContainerInterface $container,
        $name,
        callable $callback,
        ?array $options = null
    ): AbstractGuard {
        /** @var AbstractGuard $guardMiddleware */
        $guardMiddleware = $callback();
        /** @var Options $options */
        $options         = $container->get(Options::class);
        $strategies = $options->getStrategies();

        $priority = 0;
        foreach ($strategies as $classOrKey => $classOrOptions) {
            if (is_int($classOrKey)) {
                $strategy = $container->get($classOrOptions);
                $priority = $classOrKey;
            } elseif (is_string($classOrKey)) {
                $strategy = $container->get($classOrKey);
                $priority++;
            } else {
                throw new ServiceNotCreatedException('Invalid strategy provided');
            }
            $strategy->attach($guardMiddleware->getEventManager(), $priority);
        }

        return $guardMiddleware;
    }
}
