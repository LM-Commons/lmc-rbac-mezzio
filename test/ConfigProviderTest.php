<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio;

use Lmc\Rbac\Mezzio\ConfigProvider;
use PHPUnit\Framework\TestCase;

final class ConfigProviderTest extends TestCase
{
    public function testConfigProvider(): void
    {
        $configProvider = new ConfigProvider();
        $config         = $configProvider();
        $this->assertArrayHasKey('dependencies', $config);
    }
}
