<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Laminas\EventManager\Event;
use Lmc\Authentication\UserInterface;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Mezzio\Router\RouterInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;

class RedirectStrategy extends AbstractStrategy
{
    public function __construct(
        private readonly RedirectStrategyOptions $redirectStrategyOptions,
        private RouterInterface                  $router,
        private ResponseFactoryInterface         $responseFactory,
    ) {
    }

    public function onUnAuthorized(Event $event): null|ResponseInterface
    {
        if ($event->getParam('request') instanceof RequestInterface) {
            /** @var RequestInterface $request */
            $request = $event->getParam('request');
            if (null !== $request->getAttribute(UserInterface::class)) {
                if (! $this->redirectStrategyOptions->getRedirectWhenConnected()) {
                    return null;
                }
                $redirectRoute = $this->redirectStrategyOptions->getRedirectToRouteConnected();
            } else {
                $redirectRoute = $this->redirectStrategyOptions->getRedirectToRouteDisconnected();
            }
            if ($this->redirectStrategyOptions->getAppendPreviousUri()) {
                $uri = $this->router->generateUri($redirectRoute [], [
                    'query' => [
                        $this->redirectStrategyOptions->getPreviousUriQueryKey() => $request->getUri(),
                    ]
                ]);
            } else {
                $uri = $this->router->generateUri($redirectRoute []);
            }

            return $this->responseFactory
                ->createResponse(302)
                ->withHeader('Location', $uri);
        }
        return null;
    }
}
