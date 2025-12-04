<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Middleware;

use Exception;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Lmc\Rbac\Mezzio\Event\AuthorizationEvent;
use Lmc\Rbac\Mezzio\Exception\UnauthorizedException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

use function in_array;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class UnauthorizedHandler implements MiddlewareInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    public function __construct(
        private readonly Options $options,
    ) {
    }

    /**
     * @throws Exception
     * @throws Throwable
     */
    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (UnauthorizedException $exception) {
            return $this->handleUnauthorizedException($request, $handler, $exception);
        } catch (Throwable $exception) {
            if (in_array($exception->getCode(), $this->options->getExceptionCodes())) {
                return $this->handleUnauthorizedException($request, $handler, $exception);
            }
            throw $exception;
        }
    }

    protected function handleUnauthorizedException(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler,
        mixed $exception,
    ): ResponseInterface {
        // not granted, go through strategies
        $event   = new AuthorizationEvent(
            AuthorizationEvent::EVENT_UNAUTHORIZED,
            $this,
            [
                'exception' => $exception,
                'request'   => $request,
                'error'     => GuardInterface::GUARD_UNAUTHORIZED,
            ]
        );
        $results = $this->getEventManager()->triggerEventUntil(function (null|ResponseInterface $result) {
            return $result instanceof ResponseInterface;
        },
            $event);
        if ($results->last() instanceof ResponseInterface) {
            /** @psalm-suppress MixedReturnStatement */
            return $results->last();
        }
        throw $exception;
    }
}
