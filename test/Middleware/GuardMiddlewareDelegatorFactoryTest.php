<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Middleware;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Lmc\Rbac\Mezzio\Middleware\GuardMiddlewareDelegatorFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use LmcTest\Rbac\Mezzio\Assets\TestGuard;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

class GuardMiddlewareDelegatorFactoryTest extends TestCase
{
    public function testInvokeStrategyAsStringClass(): void
    {
        $strategy = $this->createMock(AbstractStrategy::class);
        $options = new Options();
        $options->setStrategies([1 => 'foo::class']);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                ['foo::class', $strategy]
            ]);
        $guard = new TestGuard();
        $events = new EventManager();
        $guard->setEventManager($events);
        $strategy->expects($this->once())->method('attach')
            ->with($events, 1);

        $callable = function () use ($guard): TestGuard {
            return $guard;
        };
        $delegator = new GuardMiddlewareDelegatorFactory();
        self::assertSame($guard, $delegator($container, 'foo', $callable));
    }

    public function testInvokeStrategyAsClass(): void
    {
        $strategy = $this->createMock(AbstractStrategy::class);
        $options = new Options();
        $options->setStrategies(['foo::class' => []]);

        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                ['foo::class', $strategy]
            ]);
        $guard = new TestGuard();
        $events = new EventManager();
        $guard->setEventManager($events);
        $strategy->expects($this->once())->method('attach')
            ->with($events, 1);

        $callable = function () use ($guard): TestGuard {
            return $guard;
        };
        $delegator = new GuardMiddlewareDelegatorFactory();
        self::assertSame($guard, $delegator($container, 'foo', $callable));
    }
}
