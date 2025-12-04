<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Exception\UnauthorizedException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Middleware\GuardMiddleware;
use Lmc\Rbac\Mezzio\Options\Options;
use Mezzio\Router\RouteResult;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[CoversClass(GuardMiddleware::class)]
final class GuardMiddlewareTest extends TestCase
{
    /** @var ServerRequestInterface&MockObject */
    protected ServerRequestInterface $request;

    /** @var RequestHandlerInterface&MockObject */
    protected RequestHandlerInterface $handler;

    #[Override]
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
        $guards     = [$this->createMock(GuardInterface::class)];
        $middleware = new GuardMiddleware($this->createMock(Options::class), $guards);
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

        $guards     = [$this->createMock(GuardInterface::class)];
        $middleware = new GuardMiddleware($this->createMock(Options::class), $guards);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testIsGrantedPolicyAllow(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(GuardInterface::class);
        $options     = $this->createMock(Options::class);

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

        $options->expects($this->once())->method('getProtectionPolicy')
            ->willReturn(GuardInterface::POLICY_ALLOW);

        $middleware = new GuardMiddleware($options, [$routeGuard]);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testIsGrantedPolicyDeny(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(GuardInterface::class);
        $options     = $this->createMock(Options::class);

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

        $options->expects($this->once())->method('getProtectionPolicy')
            ->willReturn(GuardInterface::POLICY_DENY);

        $middleware = new GuardMiddleware($options, [$routeGuard]);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testIsGrantedMultipleGuards(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard1 = $this->createMock(GuardInterface::class);
        $routeGuard2 = $this->createMock(GuardInterface::class);
        $options     = $this->createMock(Options::class);

        // return false to mock route not found
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn('foo');

        $routeGuard1->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(false);
        $routeGuard2->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(true);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);

        $options->expects($this->once())->method('getProtectionPolicy')
            ->willReturn(GuardInterface::POLICY_DENY);

        $middleware = new GuardMiddleware($options, [$routeGuard1, $routeGuard2]);
        self::assertSame($response, $middleware->process($this->request, $this->handler));
    }

    public function testNotGranted(): void
    {
        $response    = $this->createMock(ResponseInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $routeGuard  = $this->createMock(GuardInterface::class);
        $options     = $this->createMock(Options::class);

        // return false to mock route not found
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn('foo');

        $routeGuard->expects($this->once())->method('isGranted')
            ->with($this->request)
            ->willReturn(false);

        $this->request->expects($this->once())->method('getAttribute')
            ->with(RouteResult::class)
            ->willReturn($routeResult);

        $this->handler->expects($this->never())->method('handle')
            ->with($this->request)
            ->willReturn($response);

        $options->expects($this->once())->method('getProtectionPolicy')
            ->willReturn(GuardInterface::POLICY_ALLOW);

        $middleware = new GuardMiddleware($options, [$routeGuard]);
        $this->expectException(UnauthorizedException::class);
        $middleware->process($this->request, $this->handler);
    }
}
