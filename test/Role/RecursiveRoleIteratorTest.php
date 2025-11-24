<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Role;

use Laminas\Permissions\Rbac\Role;
use Laminas\Permissions\Rbac\RoleInterface;
use Lmc\Rbac\Mezzio\Role\RecursiveRoleIterator;
use LmcTest\Rbac\Mezzio\Assets\TestTraversableRoles;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use RecursiveIteratorIterator;
use stdClass;

#[CoversClass(RecursiveRoleIterator::class)]
final class RecursiveRoleIteratorTest extends TestCase
{
    public function testIsValid(): void
    {
        $roles        = [
            new Role('foo'),
        ];
        $roleIterator = new RecursiveRoleIterator($roles);
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
        }
    }

    public function testIsValidTraversable(): void
    {
        $roles        = new TestTraversableRoles([
            new Role('foo'),
        ]);
        $roleIterator = new RecursiveRoleIterator($roles);
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
        }
    }

    public function testWithChildren(): void
    {
        $parent = new Role('foo');
        $child  = new Role('bar');
        $parent->addChild($child);
        $roles        = [$parent];
        $roleIterator = new RecursiveIteratorIterator(
            new RecursiveRoleIterator($roles),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $count        = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    public function testWithInvalidChildren(): void
    {
        $parent = new Role('foo');
        $child  = new Role('bar');
        $parent->addChild($child);
        $roles        = [$parent];
        $roleIterator = new RecursiveIteratorIterator(
            new RecursiveRoleIterator($roles),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $count        = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(2, $count);
    }

    public function testWithInvalidItems(): void
    {
        $roles        = [new Role('foo'), new stdClass()];
        $roleIterator = new RecursiveIteratorIterator(
            new RecursiveRoleIterator($roles),
            RecursiveIteratorIterator::SELF_FIRST
        );
        $count        = 0;
        foreach ($roleIterator as $role) {
            $this->assertInstanceOf(RoleInterface::class, $role);
            $count++;
        }
        $this->assertEquals(1, $count);
    }
}
