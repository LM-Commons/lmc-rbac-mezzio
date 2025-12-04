<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio;

use Lmc\Rbac\Mezzio\Middleware\UnauthorizedHandlerDelegatorFactory;
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
                Options\Options::class                => Options\OptionsFactory::class,
                Guard\RouteGuard::class               => Guard\RouteGuardFactory::class,
                Guard\RoutePermissionGuard::class     => Guard\RoutePermissionGuardFactory::class,
                Guard\GuardPluginManager::class       => Guard\GuardPluginManagerFactory::class,
                Service\RoleServiceInterface::class   => Service\RoleServiceFactory::class,
                Middleware\GuardMiddleware::class     => Middleware\GuardMiddlewareFactory::class,
                Middleware\UnauthorizedHandler::class => Middleware\UnauthorizedHandlerFactory::class,
                RedirectStrategy::class               => RedirectStrategyFactory::class,
                UnauthorizedStrategy::class           => UnauthorizedStrategyFactory::class,
            ],
            'delegators' => [
                Middleware\UnauthorizedHandler::class => [
                    UnauthorizedHandlerDelegatorFactory::class,
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
