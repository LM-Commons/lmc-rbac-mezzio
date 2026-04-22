<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Guard;

use Psr\Http\Message\ServerRequestInterface;

/**
 * Interface that each guard must implement
 *
 * A guard is a lightweight security layer that occurs typically after the route has been matched. LmcRbacMvc
 * provides built-in implementations that can guard your routes and/or controllers.
 *
 * A guard can be used to block, for instance, a whole route hierarchy (all admin routes). However, only
 * using guards is not enough and rather limited, and you should protect your services using the
 * proper authorization service (see the doc for more details)
 */
interface GuardInterface
{
    /**
     * Constant for guard that can be added to the event result
     */
    public const string GUARD_UNAUTHORIZED = 'guard-unauthorized';

    /**
     * Protection policy constants
     */
    public const string POLICY_DENY  = 'deny';
    public const string POLICY_ALLOW = 'allow';

    /**
     * Condition constants
     */
    public const string CONDITION_OR  = 'OR';
    public const string CONDITION_AND = 'AND';

    public function isGranted(ServerRequestInterface $request): bool;
}
