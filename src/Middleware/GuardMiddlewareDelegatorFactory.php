<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use Psr\Container\ContainerInterface;

use function is_int;

class GuardMiddlewareDelegatorFactory
{
    public function __invoke(
        ContainerInterface $container,
        string $name,
        callable $callback,
        ?array $options = null
    ): AbstractGuardMiddleware {
        /** @var AbstractGuardMiddleware $guardMiddleware */
        $guardMiddleware = $callback();
        /** @var Options $options */
        $options    = $container->get(Options::class);
        $strategies = $options->getStrategies();

        $priority = 0;
        foreach ($strategies as $classOrKey => $classOrOptions) {
            if (is_int($classOrKey)) {
                /** @var AbstractStrategy $strategy  */
                $strategy = $container->get($classOrOptions);
                $priority = $classOrKey;
            } else {
                /** @var AbstractStrategy $strategy  */
                $strategy = $container->get($classOrKey);
                $priority++;
            }

            $strategy->attach($guardMiddleware->getEventManager(), $priority);
        }

        return $guardMiddleware;
    }
}
