<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Options;

use Lmc\Rbac\Mezzio\Exception\ServiceNotCreatedException;
use Psr\Container\ContainerInterface;

class OptionsFactory
{
    public function __invoke(ContainerInterface $container): Options
    {
        /** @var array $config */
        $config = $container->get('config');
        if (! isset($config['lmc_rbac'])) {
            throw new ServiceNotCreatedException("config 'lmc_rbac' is required");
        }
        return new Options($config['lmc_rbac']);
    }
}
