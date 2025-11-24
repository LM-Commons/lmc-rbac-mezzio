<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Options;

use Laminas\ServiceManager\ServiceManager;
use Lmc\Rbac\Mezzio\Exception\ServiceNotCreatedException;
use Lmc\Rbac\Mezzio\Guard\GuardInterface;
use Lmc\Rbac\Mezzio\Options\OptionsFactory;
use Lmc\Rbac\Mezzio\Options\RedirectStrategyOptions;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerExceptionInterface;

use function count;

#[CoversClass(OptionsFactory::class)]
final class OptionsFactoryTest extends TestCase
{
    /**
     * @throws ContainerExceptionInterface
     */
    public function testFactory(): void
    {
        $config = ['lmc_rbac' => []];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $factory = new OptionsFactory();
        $options = $factory($serviceManager);

        $this->assertEquals(0, count($options->getGuards()));
        $this->assertEquals(GuardInterface::POLICY_ALLOW, $options->getProtectionPolicy());
        $this->assertInstanceOf(RedirectStrategyOptions::class, $options->getRedirectStrategyOptions());
        $this->assertInstanceOf(UnauthorizedStrategyOptions::class, $options->getUnauthorizedStrategyOptions());
    }

    /**
     * @throws ContainerExceptionInterface
     */
    public function testFactoryNotCreatedException(): void
    {
        $config = [];

        $serviceManager = new ServiceManager();
        $serviceManager->setService('config', $config);

        $this->expectException(ServiceNotCreatedException::class);
        $factory = new OptionsFactory();
        $factory($serviceManager);
    }
}
