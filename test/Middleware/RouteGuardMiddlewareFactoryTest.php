<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Middleware\RouteGuardMiddlewareFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(RouteGuardMiddlewareFactory::class)]
final class RouteGuardMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')
            ->with(RouteGuard::class)
            ->willReturn($this->createMock(RouteGuard::class));
        $factory = new RouteGuardMiddlewareFactory();
        $factory($container);
    }
}
