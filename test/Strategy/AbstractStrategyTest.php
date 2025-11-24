<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Laminas\EventManager\EventManager;
use Laminas\EventManager\EventManagerInterface;
use Lmc\Rbac\Mezzio\Middleware\AbstractGuard;
use LmcTest\Rbac\Mezzio\Assets\TestStrategy;
use PHPUnit\Framework\TestCase;

class AbstractStrategyTest extends TestCase
{
    public function testAttach(): void
    {
        $testStrategy = new TestStrategy();
        $events = $this->createMock(EventManagerInterface::class);
        $events->expects($this->once())->method('attach')
            ->with(AbstractGuard::EVENT_NAME, [$testStrategy, 'onUnAuthorized'], 10);
        ;
        $testStrategy->attach($events, 10);
    }
}
