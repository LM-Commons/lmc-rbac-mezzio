<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Options;

use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(UnauthorizedStrategyOptions::class)]
final class UnauthorizedStrategyOptionsTest extends TestCase
{
    public function testAssertDefaultValues(): void
    {
        $unauthorizedStrategyOptions = new UnauthorizedStrategyOptions();

        $this->assertEquals('error::403', $unauthorizedStrategyOptions->getTemplate());
    }

    public function testSettersAndGetters(): void
    {
        $unauthorizedStrategyOptions = new UnauthorizedStrategyOptions([
            'template' => 'error/unauthorized',
        ]);

        $this->assertEquals('error/unauthorized', $unauthorizedStrategyOptions->getTemplate());
    }
}
