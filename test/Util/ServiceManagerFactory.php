<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Util;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mezzio\ConfigProvider;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\OptionsFactory;

class ServiceManagerFactory
{
    public static function getServiceManager(): ServiceManager
    {
        $configProvider = new ConfigProvider();
        $serviceManager = new ServiceManager($configProvider->getDependencies());
        $serviceManager->setService('config', [
                'lmc_rbac' => $configProvider->getConfig(),
            ]
        );
        return $serviceManager;
    }
}
