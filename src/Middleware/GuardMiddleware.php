<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Mezzio\Router\RouteResult;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

use function assert;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class GuardMiddleware extends AbstractGuardMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly Options $options,
        private readonly array $guards,
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
        $isGranted = $this->options->getProtectionPolicy() === GuardInterface::POLICY_ALLOW;
        foreach ($this->guards as $guard) {
            assert($guard instanceof GuardInterface);

            $result = $guard->isGranted($request);
            if ($result !== $isGranted) {
                $isGranted = $result;
                break;
            }
        }

        if ($isGranted) {
            return $handler->handle($request);
        }

        // not granted, go through strategies
        $results = $this->getEventManager()->triggerUntil(function (null|ResponseInterface $result) {
            return $result instanceof ResponseInterface;
        },
        self::EVENT_NAME,
        $this,
        [
            'request' => $request,
            'error'   => GuardInterface::GUARD_UNAUTHORIZED,
        ]);
        if ($results->last() instanceof ResponseInterface) {
            return $results->last();
        }
        return $handler->handle($request);
    }
}
