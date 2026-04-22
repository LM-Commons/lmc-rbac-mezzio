<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\RedirectStrategyFactory;
use Mezzio\Router\RouterInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseFactoryInterface;

#[CoversClass(RedirectStrategyFactory::class)]
final class RedirectStrategyFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $options   = new Options();
        $container->expects($this->exactly(3))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [RouterInterface::class, $this->createStub(RouterInterface::class)],
                [ResponseFactoryInterface::class, $this->createStub(ResponseFactoryInterface::class)],
            ]);
        $factory = new RedirectStrategyFactory();
        $factory($container);
    }
}
