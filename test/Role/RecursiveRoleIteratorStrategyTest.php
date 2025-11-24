<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Role;

use Lmc\Rbac\Mezzio\Role\RecursiveRoleIteratorStrategy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Traversable;

#[CoversClass(RecursiveRoleIteratorStrategy::class)]
class RecursiveRoleIteratorStrategyTest extends TestCase
{
    public function testGetRecursiveRoleIterator(): void
    {
        $roles = [];

        $strategy = new RecursiveRoleIteratorStrategy();
        $iterator = $strategy->getRolesIterator($roles);
        $this->assertInstanceOf(Traversable::class, $iterator);
    }
}
