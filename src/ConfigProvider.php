<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio;

use Lmc\Rbac\Mezzio\Middleware\GuardMiddlewareDelegatorFactory;
use Lmc\Rbac\Mezzio\Strategy\RedirectStrategy;
use Lmc\Rbac\Mezzio\Strategy\RedirectStrategyFactory;
use Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategy;
use Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategyFactory;

final class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => $this->getDependencies(),
            'templates'    => $this->getTemplates(),
            'lmc_rbac'     => $this->getConfig(),
        ];
    }

    public function getDependencies(): array
    {
        return [
            'factories'  => [
                Options\Options::class                 => Options\OptionsFactory::class,
                Guard\RouteGuard::class                => Guard\RouteGuardFactory::class,
                Service\RoleService::class             => Service\RoleServiceFactory::class,
                Middleware\RouteGuardMiddleware::class => Middleware\RouteGuardMiddlewareFactory::class,
                RedirectStrategy::class                => RedirectStrategyFactory::class,
                UnauthorizedStrategy::class            => UnauthorizedStrategyFactory::class,
            ],
            'delegators' => [
                RouteGuardMiddleware::class => [
                    GuardMiddlewareDelegatorFactory::class,
                ],
            ],
        ];
    }

    public function getTemplates(): array
    {
        return [
            'paths' => [
                'error' => [__DIR__ . '/../templates/lmcrbac/error'],
            ],
        ];
    }

    public function getConfig(): array
    {
        return [];
    }
}
