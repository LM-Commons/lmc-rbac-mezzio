<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Options;

use Lmc\Rbac\Mezzio\Exception\InvalidProtectionPolicyException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use LmcTest\Rbac\Mezzio\Util\ServiceManagerFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Options::class)]
final class OptionsTest extends TestCase
{
    public function testAssertModuleDefaultOptions(): void
    {
        /** @var Options $options */
        $options = ServiceManagerFactory::getServiceManager()->get(Options::class);

        $this->assertEquals(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
        $this->assertIsArray($options->getGuards());
        $this->assertIsArray($options->getStrategies());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategyOptions());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategyOptions());
    }

    public function testSettersAndGetters(): void
    {
        $options = new Options([
            'guards'                        => [],
            'strategies'                    => [],
            'protection_policy'             => 'deny',
            'unauthorized_strategy_options' => [
                'template' => 'error/unauthorized',
            ],
            'redirect_strategy_options'     => [
                'redirect_to_route_connected'    => 'home',
                'redirect_to_route_disconnected' => 'login',
            ],
        ]);

        $this->assertEquals([], $options->getGuards());
        $this->assertEquals([], $options->getStrategies());
        $this->assertEquals('deny', $options->getProtectionPolicy());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategyOptions());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategyOptions());
    }

    public function testThrowExceptionForInvalidProtectionPolicy(): void
    {
        $this->expectException(InvalidProtectionPolicyException::class);

        $options = new Options();
        $options->setProtectionPolicy('invalid');
    }
}
