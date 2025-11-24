<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Couchbase\User;
use Laminas\EventManager\Event;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mezzio\Strategy\RedirectStrategy;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;

#[CoversClass(RedirectStrategy::class)]
class RedirectStrategyTest extends TestCase
{
    /** @var RedirectStrategyOptions  */
    protected RedirectStrategyOptions $options;

    /** @var RouterInterface&MockObject  */
    protected RouterInterface $router;

    /** @var ResponseFactoryInterface&MockObject  */
    protected ResponseFactoryInterface $responseFactory;

    protected RedirectStrategy $strategy;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->options = new RedirectStrategyOptions();
        $this->router = $this->createMock(RouterInterface::class);
        $this->responseFactory = $this->createMock(ResponseFactoryInterface::class);
        $this->strategy = new RedirectStrategy(
            $this->options,
            $this->router,
            $this->responseFactory
        );
    }

    public function testNoRequestInEvent(): void
    {
        $event = new Event();
        self::assertNull($this->strategy->onUnAuthorized($event));
    }

    public function testAuthenticatedUserNoRedirectWhenConnected(): void
    {
        $event = new Event();
        $request = $this->createMock(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $this->options->setRedirectWhenConnected(false);

        $request->expects($this->any())->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn($this->createMock(UserInterface::class));
        self::assertNull($this->strategy->onUnAuthorized($event));
    }

    public function testAuthenticatedUserRedirectWhenConnected(): void
    {
        $event = new Event();
        $request = $this->createMock(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $this->options->setRedirectWhenConnected(true);
        $this->options->setRedirectToRouteConnected('foo');
        $this->options->setAppendPreviousUri(false);

        $this->router->expects($this->once())->method('generateUri')
            ->with('foo')
            ->willReturn('bar');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'bar')
            ->willReturnSelf();

        $this->responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->expects($this->any())->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn($this->createMock(UserInterface::class));

        self::assertSame($response, $this->strategy->onUnAuthorized($event));
    }

    public function testNoAuthenticatedUser(): void
    {
        $event = new Event();
        $request = $this->createMock(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $this->options->setRedirectToRouteDisConnected('bar');
        $this->options->setAppendPreviousUri(false);

        $this->router->expects($this->once())->method('generateUri')
            ->with('bar')
            ->willReturn('foo');

        $response = $this->createMock(ResponseInterface::class);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'foo')
            ->willReturnSelf();

        $this->responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->expects($this->any())->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn(null);

        self::assertSame($response, $this->strategy->onUnAuthorized($event));
    }

    public function testAppendPreviousUri(): void
    {
        $event = new Event();
        $request = $this->createMock(ServerRequestInterface::class);
        $event->setParam('request', $request);

        $this->options->setRedirectToRouteDisConnected('bar');
        $this->options->setAppendPreviousUri(true);
        $this->options->setPreviousUriQueryKey('redirect');

        $this->router->expects($this->once())->method('generateUri')
            ->with('bar')
            ->willReturn('foo');

        $response = $this->createMock(ResponseInterface::class);

        $this->responseFactory->expects($this->once())->method('createResponse')
            ->with(302)
            ->willReturn($response);

        $request->expects($this->any())->method('getAttribute')
            ->with(UserInterface::class)
            ->willReturn(null);
        $uri = $this->createMock(UriInterface::class);
        $uri->expects($this->once())->method('__toString')
            ->willReturn('baz');
        $request->expects($this->once())->method('getUri')
            ->willReturn($uri);
        $response->expects($this->once())->method('withHeader')
            ->with('Location', 'foo?redirect=baz')
            ->willReturnSelf();

        self::assertSame($response, $this->strategy->onUnAuthorized($event));
    }
}
