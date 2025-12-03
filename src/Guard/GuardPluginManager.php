<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Laminas\ServiceManager\AbstractPluginManager;
use Lmc\Rbac\Mezzio\Exception\InvalidConfigurationException;

use function gettype;
use function is_object;
use function sprintf;

/**
 * @template InstanceType
 * @extends AbstractPluginManager<InstanceType>
 * @final
 */
class GuardPluginManager extends AbstractPluginManager
{
    /** @var string[]|callable[] */
    protected $factories = [
        RouteGuard::class           => RouteGuardFactory::class,
        RoutePermissionGuard::class => RoutePermissionGuardFactory::class,
    ];

    /**
     * @inheritDoc
     */
    public function validate($instance): void
    {
        if ($instance instanceof GuardInterface) {
            return;
        }
        throw new InvalidConfigurationException(sprintf(
            'Guards must implement "Lmc\Rbac\Mezzio\Guard\GuardInterface", but "%s" was given',
            is_object($instance) ? $instance::class : gettype($instance)
        ));
    }
}
