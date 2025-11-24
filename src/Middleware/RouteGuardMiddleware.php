<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouteGuardMiddleware extends AbstractGuardMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly GuardInterface $routeGuard,
    ) {
    }

    /**
     * @inheritDoc
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        /** @var null|RouteResult $routeResult */
        $routeResult = $request->getAttribute(RouteResult::class);
        if (null === $routeResult) {
            // do nothing
            return $handler->handle($request);
        }
        $matchedRouteName = $routeResult->getMatchedRouteName();
        if (false === $matchedRouteName) {
            // Do nothing, route not found
            return $handler->handle($request);
        }

        $granted = $this->routeGuard->isGranted($request);
        if ($granted) {
            return $handler->handle($request);
        }

        $results = $this->getEventManager()->triggerUntil(function (null|ResponseInterface $result) {
            return $result instanceof ResponseInterface;
        },
        self::EVENT_NAME,
        $this,
        ['request' => $request]);
        if ($results->last() instanceof ResponseInterface) {
            return $results->last();
        }
        return $handler->handle($request);
    }
}
