<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\GuardPluginManager;
use Lmc\Rbac\Mezzio\Guard\GuardsFactory;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Options\Options;
use Override;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

final class GuardsFactoryTest extends TestCase
{
    /** @var ContainerInterface&MockObject  */
    private ContainerInterface $container;

    #[Override]
    public function setUp(): void
    {
        parent::setUp();
        $this->container = $this->createMock(ContainerInterface::class);
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testNoGuards(): void
    {
        $options = new Options();
        $this->container->expects($this->once())->method('get')
            ->with(Options::class)
            ->willReturn($options);
        $factory = new GuardsFactory();
        $this->assertEquals([], $factory($this->container));
    }

    /**
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testGuards(): void
    {
        $options            = new Options([
            'guards' => [
                RouteGuard::class => [],
            ],
        ]);
        $guardPluginManager = $this->createMock(GuardPluginManager::class);
        $guardPluginManager->expects($this->once())->method('get')
            ->with(RouteGuard::class)
            ->willReturn($this->createStub(RouteGuard::class));
        $this->container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [GuardPluginManager::class, $guardPluginManager],
            ]);
        $factory = new GuardsFactory();
        $guards  = $factory($this->container);
        $this->assertInstanceOf(GuardInterface::class, $guards[0]);
    }
}
