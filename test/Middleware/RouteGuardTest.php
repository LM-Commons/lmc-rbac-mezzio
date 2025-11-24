<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Middleware\RouteGuardMiddleware;
use LmcTest\Rbac\Mezzio\Assets\TestStrategy;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(RouteGuardMiddleware::class)]
class RouteGuardTest extends TestCase
{

    /** @var ServerRequestInterface&MockObject */
    protected ServerRequestInterface $request;

    /** @var RequestHandlerInterface&MockObject */
    protected RequestHandlerInterface $handler;

    #[\Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createMock(ServerRequestInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    public function testNoRoute(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn(null);
        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);
        $middleware = new RouteGuardMiddleware($this->createMock(GuardInterface::class));
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testRouteNotFound(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);

        // return false to mock route not found
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn(false);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);

        $middleware = new RouteGuardMiddleware($this->createMock(GuardInterface::class));
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testIsGranted(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(RouteGuard::class);

        // return false to mock route not found
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn('foo');

        $routeGuard->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(true);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);

        $middleware = new RouteGuardMiddleware($routeGuard);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testNotGrantedNoStrategy(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(RouteGuard::class);

        // return false to mock route not found
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn('foo');

        $routeGuard->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(false);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);

        $middleware = new RouteGuardMiddleware($routeGuard);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testNotGrantedWithStrategy(): void
    {
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(RouteGuard::class);

        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn('foo');

        $routeGuard->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(false);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->never())->method('handle');

        $strategy = new TestStrategy();

        $middleware = new RouteGuardMiddleware($routeGuard);
        $strategy->attach($middleware->getEventManager());

        self::assertInstanceOf(ResponseInterface::class, $middleware->process($this->request, $this->handler));
    }

}
