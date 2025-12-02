<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuard;
use Lmc\Rbac\Mezzio\Middleware\RoutePermissionGuardMiddlewareFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(RoutePermissionGuardMiddlewareFactory::class)]
final class RoutePermissionGuardMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')
            ->with(RoutePermissionGuard::class)
            ->willReturn($this->createMock(RoutePermissionGuard::class));
        $factory = new RoutePermissionGuardMiddlewareFactory();
        $factory($container);
    }
}
