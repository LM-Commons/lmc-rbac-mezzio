<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use Laminas\Permissions\Rbac\Rbac;
use Lmc\Rbac\Assertion\AssertionPluginManager;
use Lmc\Rbac\Identity\IdentityInterface;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RoutePermissionGuard;
use Lmc\Rbac\Role\InMemoryRoleProvider;
use Lmc\Rbac\Service\AuthorizationService;
use Lmc\Rbac\Service\AuthorizationServiceInterface;
use Lmc\Rbac\Service\RoleService;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(RoutePermissionGuard::class)]
final class RoutePermissionsGuardTest extends TestCase
{
    public function testCreateRoutePermissionsGuardDefault(): void
    {
        $authorizationService = $this->createMock(AuthorizationServiceInterface::class);
        $routeGuard           = new RoutePermissionGuard($authorizationService);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }

    public function testSetPolicyRouteGuard(): void
    {
        $authorizationService = $this->createMock(AuthorizationServiceInterface::class);
        $routeGuard           = new RoutePermissionGuard($authorizationService, [], GuardInterface::POLICY_DENY);
        $this->assertEquals(GuardInterface::POLICY_DENY, $routeGuard->getProtectionPolicy());

        $routeGuard->setProtectionPolicy(GuardInterface::POLICY_ALLOW);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }

    #[DataProvider('rulesProvider')]
    public function testRulesRoutePermissionsGuard(
        string $routeName,
        array $rules,
        array $roles,
        string $policy,
        bool $isAllowed,
    ): void {
        $authorizationService = $this->getAuthorizationService();
        $request              = $this->createMock(ServerRequestInterface::class);
        $routeResult          = $this->createMock(RouteResult::class);
        $identity             = $this->createMock(IdentityInterface::class);
        $identity->expects($this->any())->method('getRoles')->willReturn($roles);
        $request->expects($this->any())->method('getAttribute')
            ->willReturnMap([
                [RouteResult::class, null, $routeResult],
                [UserInterface::class, null, $identity],
            ]);
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn($routeName);
        $routePermissionGuard = new RoutePermissionGuard($authorizationService, $rules, $policy);
        self::assertEquals($isAllowed, $routePermissionGuard->isGranted($request));
    }

    public static function rulesProvider(): array
    {
        return [
            'test_no_rules_policy_allowed'                           => [
                'routeName' => 'foo',
                'rules'     => [],
                'roles'     => [],
                'policy'    => GuardInterface::POLICY_ALLOW,
                'isAllowed' => true,
            ],
            'test_no_rules_policy_deny'                              => [
                'routeName' => 'foo',
                'rules'     => [],
                'roles'     => [],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_all_rules'                                         => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => ['*'],
                ],
                'roles'     => ['user'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_match'                                => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => ['read'],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_no_match'                             => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => ['write'],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_simple_rules_multiple_no_conditions'               => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => ['read', 'delete'],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_multiple_no_conditions_fail'          => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => ['read', 'write'],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_simple_rules_multiple_explicit_permissions'        => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['read', 'delete'],
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_multiple_explicit_permissions_fail'   => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['read', 'write'],
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_simple_rules_multiple_explicit_condition'          => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['read', 'delete'],
                        'condition'   => GuardInterface::CONDITION_AND,
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_multiple_explicit_condition_fail'     => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['read', 'write'],
                        'condition'   => GuardInterface::CONDITION_AND,
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_simple_rules_multiple_explicit_condition_or'       => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['read', 'write'],
                        'condition'   => GuardInterface::CONDITION_OR,
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_multiple_explicit_condition_or_fail'  => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['create', 'write'],
                        'condition'   => GuardInterface::CONDITION_OR,
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_simple_rules_multiple_explicit_condition_failsafe' => [
                'routeName' => 'foo',
                'rules'     => [
                    'foo' => [
                        'permissions' => ['create', 'write'],
                        'condition'   => 'bar',
                    ],
                ],
                'roles'     => ['admin'],
                'policy'    => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
        ];
    }

    private function getAuthorizationService(): AuthorizationServiceInterface
    {
        $roleProvider = new InMemoryRoleProvider([
            'user',
            'admin' => [
                'permissions' => ['read', 'delete'],
            ],
        ]);
        $roleService  = new RoleService($roleProvider, 'guest');
        return new AuthorizationService(
            new Rbac(),
            $roleService,
            new AssertionPluginManager(
                $this->createMock(ContainerInterface::class),
                []
            )
        );
    }
}
