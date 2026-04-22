<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Exception;
use Lmc\Rbac\Mezzio\Exception\UnauthorizedException;
use Lmc\Rbac\Mezzio\Middleware\UnauthorizedHandler;
use Lmc\Rbac\Mezzio\Options\Options;
use LmcTest\Rbac\Mezzio\Assets\TestStrategy;
use Override;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Throwable;

#[CoversClass(UnauthorizedHandler::class)]
final class UnauthorizedHandlerTest extends TestCase
{
    protected ServerRequestInterface&Stub $request;

    protected RequestHandlerInterface&MockObject $handler;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->request = $this->createStub(ServerRequestInterface::class);
        $this->handler = $this->createMock(RequestHandlerInterface::class);
    }

    /**
     * @throws Throwable
     */
    public function testNoException(): void
    {
        $options  = new Options();
        $handler  = new UnauthorizedHandler($options);
        $response = $this->createStub(ResponseInterface::class);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willReturn($response);
        $handler->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testNon403ThrowableException(): void
    {
        $options = new Options();
        $handler = new UnauthorizedHandler($options);
        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willThrowException(new Exception('foo', 1));
        $this->expectException(Exception::class);
        $handler->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testUnauthorizedExceptionNoStrategy(): void
    {
        $options = new Options();
        $handler = new UnauthorizedHandler($options);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willThrowException(new UnauthorizedException());
        $this->expectException(UnauthorizedException::class);
        $handler->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function test403ExceptionNoStrategy(): void
    {
        $options = new Options();
        $handler = new UnauthorizedHandler($options);

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willThrowException(new Exception('foo', 403));
        $this->expectException(Exception::class);
        $handler->process($this->request, $this->handler);
    }

    /**
     * @throws Throwable
     */
    public function testNotGrantedWithStrategy(): void
    {
        $options  = new Options();
        $strategy = new TestStrategy();

        $this->handler->expects($this->once())->method('handle')
            ->with($this->request)
            ->willThrowException(new UnauthorizedException());

        $handler = new UnauthorizedHandler($options);
        $strategy->attach($handler->getEventManager());

        self::assertInstanceOf(ResponseInterface::class, $handler->process($this->request, $this->handler));
    }
}
