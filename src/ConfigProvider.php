<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'lmc_rbac'     => $this->getConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories' => [
                Options\Options::class                => Options\OptionsFactory::class,
                Guard\RouteGuard::class               => Guard\RouteGuardFactory::class,
                Middleware\RbacGuardMiddleware::class => Middleware\RbacGuardMiddlewareFactory::class,
            ],
        ];
    }

    public function getConfig(): array
    {
        return [
        ];
    }
}
