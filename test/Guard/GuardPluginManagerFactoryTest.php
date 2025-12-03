<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Guard\GuardPluginManager;
use Lmc\Rbac\Mezzio\Guard\GuardPluginManagerFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class GuardPluginManagerFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $options   = new Options();
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->once())->method('get')->with(Options::class)->willReturn($options);
        $factory = new GuardPluginManagerFactory();
        $this->assertInstanceOf(GuardPluginManager::class, $factory($container));
    }
}
