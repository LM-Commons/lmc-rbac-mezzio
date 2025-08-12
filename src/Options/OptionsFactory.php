<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Options;

use Laminas\ServiceManager\Exception\ServiceNotCreatedException;
use Laminas\ServiceManager\Factory\FactoryInterface;
use Psr\Container\ContainerInterface;

class OptionsFactory implements FactoryInterface
{

    /**
     * @inheritDoc
     */
    public function __invoke(ContainerInterface $container, $requestedName, ?array $options = null): Options
    {
        /** @var array $config */
        $config = $container->get('config');
        if (! isset($config['lmc_rbac'])) {
            throw new ServiceNotCreatedException("config 'lmc_rbac' is required");
        }
        return new Options($config['lmc_rbac']);
    }
}
