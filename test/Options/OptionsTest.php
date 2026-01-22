<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Options;

use Laminas\Stdlib\ArrayUtils;
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
        $options = ServiceManagerFactory::getServiceManager()->get(Options::class);

        $this->assertEquals(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
        $this->assertIsArray($options->getGuards());
        $this->assertIsArray($options->getStrategies());
        $this->assertIsArray($options->getGuardManager());
        $this->assertArrayHasKey('factories', $options->getGuardManager());
        $this->assertIsArray($options->getExceptionCodes());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategyOptions());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategyOptions());
    }

    public function testSettersAndGetters(): void
    {
        $options = new Options([
            'unauthorized_strategy_options' => [
                'template' => 'error/unauthorized',
            ],
            'redirect_strategy_options'     => [
                'redirect_to_route_connected'    => 'home',
                'redirect_to_route_disconnected' => 'login',
            ],
        ]);
        $options->setGuards(['foo' => 'bar']);
        $this->assertEquals(['foo' => 'bar'], $options->getGuards());
        $this->assertEquals('bar', $options->getGuardOptions('foo'));
        $this->assertEquals([], $options->getGuardOptions('baz'));
        $options->setStrategies(['foo']);
        $this->assertEquals(['foo'], $options->getStrategies());
        $options->setProtectionPolicy('deny');
        $this->assertEquals('deny', $options->getProtectionPolicy());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategyOptions());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategyOptions());
        $options->setExceptionCodes(['foo']);
        $this->assertEquals(['foo'], $options->getExceptionCodes());
        $guardManager = $options->getGuardManager();
        $options->setGuardManager(['factories' => ['foo' => 'bar']]);
        $this->assertEquals(ArrayUtils::merge(
            $guardManager,
            ['factories' => ['foo' => 'bar']]
        ), $options->getGuardManager());
    }

    public function testThrowExceptionForInvalidProtectionPolicy(): void
    {
        $this->expectException(InvalidProtectionPolicyException::class);

        $options = new Options();
        $options->setProtectionPolicy('invalid');
    }
}
