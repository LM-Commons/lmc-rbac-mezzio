<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;

abstract class AbstractGuardMiddleware implements EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    public const string EVENT_NAME = 'unauthorized';
}
