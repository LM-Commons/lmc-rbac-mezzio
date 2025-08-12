<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Options;

use Laminas\Stdlib\AbstractOptions;

/**
 * Redirect strategy options
 *
 * @template TValue
 * @extends AbstractOptions<TValue>
 */
class RedirectStrategyOptions extends AbstractOptions
{
    /**
     * Should the user be redirected when connected and not authorized
     */
    protected bool $redirectWhenConnected = true;

    /**
     * The name of the route to redirect when a user is connected and not authorized
     */
    protected string $redirectToRouteConnected = 'home';

    /**
     * The name of the route to redirect when a user is disconnected and not authorized
     */
    protected string $redirectToRouteDisconnected = 'login';

    /**
     * Should the previous URI should be appended as a query param?
     */
    protected bool $appendPreviousUri = true;

    /**
     * If appendPreviousUri is enabled, key to use in query params that hold the previous URI
     */
    protected string $previousUriQueryKey = 'redirectTo';

    public function setRedirectWhenConnected(bool $redirectWhenConnected): void
    {
        $this->redirectWhenConnected = $redirectWhenConnected;
    }

    public function getRedirectWhenConnected(): bool
    {
        return $this->redirectWhenConnected;
    }

    public function setRedirectToRouteConnected(string $redirectToRouteConnected): void
    {
        $this->redirectToRouteConnected = $redirectToRouteConnected;
    }

    public function getRedirectToRouteConnected(): string
    {
        return $this->redirectToRouteConnected;
    }

    public function setRedirectToRouteDisconnected(string $redirectToRouteDisconnected): void
    {
        $this->redirectToRouteDisconnected = $redirectToRouteDisconnected;
    }

    public function getRedirectToRouteDisconnected(): string
    {
        return $this->redirectToRouteDisconnected;
    }

    public function setAppendPreviousUri(bool $appendPreviousUri): void
    {
        $this->appendPreviousUri = $appendPreviousUri;
    }

    public function getAppendPreviousUri(): bool
    {
        return $this->appendPreviousUri;
    }

    public function setPreviousUriQueryKey(string $previousUriQueryKey): void
    {
        $this->previousUriQueryKey = $previousUriQueryKey;
    }

    public function getPreviousUriQueryKey(): string
    {
        return $this->previousUriQueryKey;
    }
}
