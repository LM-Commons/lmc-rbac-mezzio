<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio;

use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Guard\RouteGuardFactory;

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
                Options\Options::class => Options\OptionsFactory::class,
                RouteGuard::class      => RouteGuardFactory::class,
            ],
        ];
    }

    public function getConfig(): array
    {
        return [
        ];
    }
}
