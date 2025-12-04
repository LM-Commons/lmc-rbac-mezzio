<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Laminas\EventManager\EventManager;
use Lmc\Rbac\Mezzio\Middleware\UnauthorizedHandlerDelegatorFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use LmcTest\Rbac\Mezzio\Assets\TestUnauthorizedHandler;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(UnauthorizedHandlerDelegatorFactory::class)]
final class UnauthorizedHandlerDelegatorFactoryTest extends TestCase
{
    public function testInvokeStrategyAsStringClass(): void
    {
        $strategy = $this->createMock(AbstractStrategy::class);
        $options  = new Options();
        $options->setStrategies([1 => 'foo::class']);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                ['foo::class', $strategy],
            ]);
        $handler = new TestUnauthorizedHandler();
        $events  = new EventManager();
        $handler->setEventManager($events);
        $strategy->expects($this->once())->method('attach')
            ->with($events, 1);

        $callable  = function () use ($handler): TestUnauthorizedHandler {
            return $handler;
        };
        $delegator = new UnauthorizedHandlerDelegatorFactory();
        self::assertSame($handler, $delegator($container, 'foo', $callable));
    }

    public function testInvokeStrategyAsClass(): void
    {
        $strategy = $this->createMock(AbstractStrategy::class);
        $options  = new Options();
        $options->setStrategies(['foo::class' => []]);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                ['foo::class', $strategy],
            ]);
        $handler = new TestUnauthorizedHandler();
        $events  = new EventManager();
        $handler->setEventManager($events);
        $strategy->expects($this->once())->method('attach')
            ->with($events, 1);

        $callable  = function () use ($handler): TestUnauthorizedHandler {
            return $handler;
        };
        $delegator = new UnauthorizedHandlerDelegatorFactory();
        self::assertSame($handler, $delegator($container, 'foo', $callable));
    }
}
