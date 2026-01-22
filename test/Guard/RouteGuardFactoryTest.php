<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use AssertionError;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Guard\RouteGuardFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class RouteGuardFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testInvoke(): void
    {
        $options = new Options();
        $options->setGuards([
            RouteGuard::class => [],
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [RoleServiceInterface::class, $this->createMock(RoleServiceInterface::class)],
            ]);
        $factory = new RouteGuardFactory();
        $factory($container);
    }

    public function testInvalidRules(): void
    {
        $options = new Options();
        $options->setGuards([
            RouteGuard::class => 'foo',
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(1))->method('get')
            ->willReturnMap([
                [Options::class, $options],
            ]);
        $this->expectException(AssertionError::class);
        $factory = new RouteGuardFactory();
        $factory($container);
    }
}
