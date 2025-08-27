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
                Options\Options::class                 => Options\OptionsFactory::class,
                Guard\RouteGuard::class                => Guard\RouteGuardFactory::class,
                Service\RoleService::class             => Service\RoleServiceFactory::class,
                Middleware\RouteGuardMiddleware::class => Middleware\RouteGuardMiddlewareFactory::class,
            ],
        ];
    }

    public function getConfig(): array
    {
        return [
        ];
    }
}
