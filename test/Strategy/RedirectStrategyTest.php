<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Laminas\EventManager\Event;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mezzio\Strategy\RedirectStrategy;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(RedirectStrategy::class)]
final class RedirectStrategyTest extends TestCase
{
    public function testNoRequestInEvent(): void
    {
        $event    = new Event();
        $strategy = new RedirectStrategy(
            new RedirectStrategyOptions(),
            $this->createStub(RouterInterface::class),
            $this->createStub(ResponseFactoryInterface::class)
        );
        self::assertNull($strategy->onUnAuthorized($event));
    }

    public function testAuthenticatedUserNoRedirectWhenConnected(): void
    {
        $event   = new Event();
        $request = $this->createStub(ServerRequestInterface::class);
        $event->setParam('request', $request);
        $options = new RedirectStrategyOptions();

        $options->setRedirectWhenConnected(false);

        $request->method('getAttribute')
            ->willReturn($this->createStub(UserInterface::class));
        $strategy = new RedirectStrategy(
            $options,
            $this->createStub(RouterInterface::class),
            $this->createStub(ResponseFactoryInterface::class)
        );

        self::assertNull($strategy->onUnAuthorized($event));
    }

    public function testAuthenticatedUserRedirectWhenConnected(): void
    {
        $event   = new Event();
        $request = $this->createStub(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $options = new RedirectStrategyOptions();

        $options->setRedirectWhenConnected(true);
        $options->setRedirectToRouteConnected('foo');
        $options->setAppendPreviousUri(false);

        $router = $this->createMock(RouterInterface::class);

        $router->expects($this->once())->method('generateUri')
            ->with('foo')
            ->willReturn('bar');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'bar')
            ->willReturnSelf();

        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->method('getAttribute')
            ->willReturn($this->createStub(UserInterface::class));

        $strategy = new RedirectStrategy(
            $options,
            $router,
            $responseFactory
        );

        self::assertSame($response, $strategy->onUnAuthorized($event));
    }

    public function testNoAuthenticatedUser(): void
    {
        $event   = new Event();
        $request = $this->createStub(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $options = new RedirectStrategyOptions();

        $options->setRedirectToRouteDisConnected('bar');
        $options->setAppendPreviousUri(false);

        $router = $this->createMock(RouterInterface::class);

        $router->expects($this->once())->method('generateUri')
            ->with('bar')
            ->willReturn('foo');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'foo')
            ->willReturnSelf();

        $responseFactory = $this->createMock(ResponseFactoryInterface::class);

        $responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->method('getAttribute')
            ->willReturn(null);

        $strategy = new RedirectStrategy(
            $options,
            $router,
            $responseFactory
        );

        self::assertSame($response, $strategy->onUnAuthorized($event));
    }

    public function testAppendPreviousUri(): void
    {
        $event   = new Event();
        $request = $this->createMock(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $options = new RedirectStrategyOptions();
        $options->setRedirectToRouteDisConnected('bar');
        $options->setAppendPreviousUri(true);
        $options->setPreviousUriQueryKey('redirect');

        $router = $this->createMock(RouterInterface::class);
        $router->expects($this->once())->method('generateUri')
            ->with('bar')
            ->willReturn('foo');

        $response = $this->createMock(ResponseInterface::class);

        $responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->method('getAttribute')
            ->willReturn(null);

        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')
            ->willReturn('baz');
        $request->expects($this->once())->method('getUri')
            ->willReturn($uri);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'foo?redirect=baz')
            ->willReturnSelf();

        $strategy = new RedirectStrategy(
            $options,
            $router,
            $responseFactory
        );
        self::assertSame($response, $strategy->onUnAuthorized($event));
    }
}
