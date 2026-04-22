<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mezzio\Exception\InvalidConfigurationException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\GuardPluginManager;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use stdClass;

#[CoversClass(GuardPluginManager::class)]
final class GuardPluginManagerTest extends TestCase
{
    public function testValidInstance(): void
    {
        $options       = [
            'factories' => [
                'foo' => function (ContainerInterface $container): GuardInterface {
                    return $this->createStub(GuardInterface::class);
                },
            ],
        ];
        $container     = new ServiceManager();
        $pluginManager = new GuardPluginManager($container, $options);
        $this->assertInstanceOf(GuardInterface::class, $pluginManager->get('foo'));
    }

    public function testInvalidInstance(): void
    {
        $options       = [
            'factories' => [
                'foo' => function (ContainerInterface $container): stdClass {
                    return new stdClass();
                },
            ],
        ];
        $container     = new ServiceManager();
        $pluginManager = new GuardPluginManager($container, $options);
        $this->expectException(InvalidConfigurationException::class);
        $pluginManager->get('foo');
    }
}
