<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Service;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleServiceFactory;
use Lmc\Rbac\Service\RoleServiceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

final class RoleServiceFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container   = $this->createMock(ContainerInterface::class);
        $options     = $this->createStub(Options::class);
        $roleService = $this->createStub(RoleServiceInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [RoleServiceInterface::class, $roleService],
                [Options::class, $options],
            ]);
        $factory = new RoleServiceFactory();
        $factory($container);
    }
}
