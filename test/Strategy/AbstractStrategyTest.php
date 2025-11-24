<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Laminas\EventManager\EventManagerInterface;
use Lmc\Rbac\Mezzio\Middleware\AbstractGuardMiddleware;
use LmcTest\Rbac\Mezzio\Assets\TestStrategy;
use PHPUnit\Framework\TestCase;

final class AbstractStrategyTest extends TestCase
{
    public function testAttach(): void
    {
        $testStrategy = new TestStrategy();
        $events       = $this->createMock(EventManagerInterface::class);
        $events->expects($this->once())->method('attach')
            ->with(AbstractGuardMiddleware::EVENT_NAME, [$testStrategy, 'onUnAuthorized'], 10);

        $testStrategy->attach($events, 10);
    }
}
