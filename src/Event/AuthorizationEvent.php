<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Event;

use ArrayAccess;
use Laminas\EventManager\Event;

/**
 * @template TTarget of object|string|null
 * @template TParams of array|ArrayAccess|object
 * @extends Event<TTarget, TParams>
 */
final class AuthorizationEvent extends Event
{
    public const EVENT_UNAUTHORIZED = 'unauthorized';
}
