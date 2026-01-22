<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use AssertionError;
use Lmc\Rbac\Mezzio\Exception\InvalidConfigurationException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuard;
use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuardFactory;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Service\AuthorizationServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(RoutePermissionGuardFactory::class)]
final class RoutePermissionsGuardFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $options = new Options();
        $options->setGuards([
            RoutePermissionGuard::class => [],
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [AuthorizationServiceInterface::class, $this->createMock(AuthorizationServiceInterface::class)],
            ]);
        $factory = new RoutePermissionGuardFactory();
        $factory($container);
    }

    public function testInvokeInvalidRules(): void
    {
        $options = new Options();
        $options->setGuards([
            RoutePermissionGuard::class => 'foo',
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly(1))->method('get')
            ->willReturnMap([
                [Options::class, $options],
            ]);
        $this->expectException(AssertionError::class);
        $factory = new RoutePermissionGuardFactory();
        $factory($container);
    }

    #[DataProvider('rulesProvider')]
    public function testInvokeWithRules(array $rules, int $count, bool $expectException): void
    {
        $options = new Options();
        $options->setGuards([
            RoutePermissionGuard::class => $rules,
        ]);
        $container = $this->createMock(ContainerInterface::class);
        $container->expects($this->exactly($count))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [
                    AuthorizationServiceInterface::class,
                    $this->createMock(AuthorizationServiceInterface::class),
                ],
            ]);
        if ($expectException) {
            $this->expectException(InvalidConfigurationException::class);
        }
        $factory = new RoutePermissionGuardFactory();
        $factory($container);
    }

    public static function rulesProvider(): array
    {
        return [
            'route_no_permission'                 => [
                'rules'           => [
                    'foo',
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_permission_empty_array'        => [
                'rules'           => [],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_simple_permission'             => [
                'rules'           => [
                    'foo' => 'bar',
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_permission_array'              => [
                'rules'           => [
                    'foo' => ['bar'],
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_permission_permissions'        => [
                'rules'           => [
                    'foo' => [
                        'permissions' => ['foo', 'bar'],
                    ],
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_no_permission_condition_AND'   => [
                'rules'           => [
                    'foo' => [
                        'permissions' => ['foo', 'bar'],
                        'condition'   => GuardInterface::CONDITION_AND,
                    ],
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_permission_conditions_OR'      => [
                'rules'           => [
                    'foo' => [
                        'permissions' => ['foo', 'bar'],
                        'condition'   => GuardInterface::CONDITION_OR,
                    ],
                ],
                'count'           => 2,
                'expectException' => false,
            ],
            'route_permission_invalid_condition'  => [
                'rules'           => [
                    'foo' => [
                        'permissions' => ['foo', 'bar'],
                        'condition'   => 'foo',
                    ],
                ],
                'count'           => 1,
                'expectException' => true,
            ],
            'route_permission_invalid_permission' => [
                'rules'           => [
                    'foo' => [
                        'permissions' => 'foo',
                        'condition'   => 'foo',
                    ],
                ],
                'count'           => 1,
                'expectException' => true,
            ],
            'route_permission_permission_empty'   => [
                'rules'           => [
                    'foo' => [],
                ],
                'count'           => 2,
                'expectException' => false,
            ],
        ];
    }
}
