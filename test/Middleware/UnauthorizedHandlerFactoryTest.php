<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Lmc\Rbac\Mezzio\Middleware\UnauthorizedHandlerFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(UnauthorizedHandlerFactory::class)]
final class UnauthorizedHandlerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(1))->method('get')
            ->with(Options::class)
            ->willReturn(new Options());
        $factory = new UnauthorizedHandlerFactory();
        $factory($container);
    }
}
