<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Middleware\GuardMiddlewareFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(GuardMiddlewareFactory::class)]
final class GuardMiddlewareFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->with(Options::class)
            ->willReturn(new Options());
        $factory = new GuardMiddlewareFactory();
        $factory($container);
    }
}
