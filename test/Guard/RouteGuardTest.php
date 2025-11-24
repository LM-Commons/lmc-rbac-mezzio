<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Guard;

use Lmc\Rbac\Identity\IdentityInterface;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Guard\RouteGuard;
use Lmc\Rbac\Mezzio\Role\RecursiveRoleIteratorStrategy;
use Lmc\Rbac\Mezzio\Service\RoleServiceInterface;
use Lmc\Rbac\Role\InMemoryRoleProvider;
use Lmc\Rbac\Role\RoleProviderInterface;
use Lmc\Rbac\Service\RoleService;
use Mezzio\Authentication\UserInterface;
use Mezzio\Router\RouteResult;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;

#[CoversClass(RouteGuard::class)]
class RouteGuardTest extends TestCase
{
    public function testCreateRouteGuardDefault(): void
    {
        $roleService = $this->createMock(RoleServiceInterface::class);
        $routeGuard = new RouteGuard($roleService);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }

    public function testSetPolicyRouteGuard(): void
    {
        $roleService = $this->createMock(RoleServiceInterface::class);
        $routeGuard = new RouteGuard($roleService, [], GuardInterface::POLICY_DENY);
        $this->assertEquals(GuardInterface::POLICY_DENY, $routeGuard->getProtectionPolicy());

        $routeGuard->setProtectionPolicy(GuardInterface::POLICY_ALLOW);
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $routeGuard->getProtectionPolicy());
    }

    #[DataProvider('rulesProvider')]
    public function testRulesRouteGuard(
        string $routeName,
        array $rules,
        array $roles,
        string $policy,
        bool $isAllowed,
    ): void {
        $roleService = new \Lmc\Rbac\Mezzio\Service\RoleService(
            $this->getBaseRoleService(),
            new RecursiveRoleIteratorStrategy()
        );
        $request = $this->createMock(ServerRequestInterface::class);
        $routeResult = $this->createMock(RouteResult::class);
        $identity = $this->createMock(IdentityInterface::class);
        $identity->expects($this->any())->method('getRoles')->willReturn($roles);

        $request->expects($this->any())->method('getAttribute')
            ->willReturnMap([
                [RouteResult::class, $routeResult],
                [UserInterface::class, $identity],
            ]);
        $routeResult->expects($this->once())->method('getMatchedRouteName')->willReturn($routeName);
        $routeGuard = new RouteGuard($roleService, $rules, $policy);
        self::assertEquals($isAllowed, $routeGuard->isGranted($request));
    }

    static public function rulesProvider(): array
    {
        return [
            'test_no_rules_policy_allowed' => [
                'routeName' => 'foo',
                'rules' => [],
                'roles' => [],
                'policy' => GuardInterface::POLICY_ALLOW,
                'isAllowed' => true,
            ],
            'test_no_rules_policy_deny' => [
                'routeName' => 'foo',
                'rules' => [],
                'roles' => [],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_all_rules' => [
                'routeName' => 'foo',
                'rules' => [
                    'foo' => ['*']
                ],
                'roles' => ['user'],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_match' => [
                'routeName' => 'foo',
                'rules' => [
                    'foo' => ['user']
                ],
                'roles' => ['user'],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
            'test_simple_rules_no_match' => [
                'routeName' => 'foo',
                'rules' => [
                    'foo' => ['user']
                ],
                'roles' => ['admin'],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_rule_route_no_roles' => [
                'routeName' => 'foo',
                'rules' => [
                    'foo',
                ],
                'roles' => ['user'],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => false,
            ],
            'test_rule_partialroute' => [
                'routeName' => 'foobar',
                'rules' => [
                    'foo*' => ['user']
                ],
                'roles' => ['user'],
                'policy' => GuardInterface::POLICY_DENY,
                'isAllowed' => true,
            ],
        ];
    }

    protected function getBaseRoleService(): RoleService
    {
        $roleProvider = new InMemoryRoleProvider([
            ['user']
        ]);
        return new RoleService($roleProvider, 'guest');
    }
}
