<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Lmc\Rbac\Mezzio\Options\Options;
use Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategyFactory;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Container\ContainerInterface;

#[CoversClass(UnauthorizedStrategyFactory::class)]
class UnauthorizedStrategyFactoryTest extends TestCase
{
    public function testInvoke(): void
    {
        $container = $this->createMock(ContainerInterface::class);
        $options = new Options();
        $container->expects($this->exactly(2))->method('get')
            ->willReturnMap([
                [Options::class, $options],
                [TemplateRendererInterface::class, $this->createMock(TemplateRendererInterface::class)],
            ]);
        $factory = new UnauthorizedStrategyFactory();
        $factory($container);
    }
}
