<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Laminas\EventManager\EventManagerInterface;
use Laminas\EventManager\ListenerAggregateTrait;

abstract class AbstractGuard implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    public const EVENT_NAME = 'unauthorized';
}
