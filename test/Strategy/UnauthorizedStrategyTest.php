<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Strategy;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\EventManager\Event;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use Lmc\Rbac\Mezzio\Strategy\UnauthorizedStrategy;
use Mezzio\Template\TemplateRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\RequestInterface;

#[CoversClass(UnauthorizedStrategy::class)]
class UnauthorizedStrategyTest extends TestCase
{

    public function testNoRequestInEvent(): void
    {
        $options = new UnauthorizedStrategyOptions();

        $unauthorizedStrategy = new UnauthorizedStrategy(
            $options,
            $this->createMock(TemplateRendererInterface::class)
        );
        $event = new Event();
        self::assertEquals(null, $unauthorizedStrategy->onUnAuthorized($event));
    }

    public function testRequestInEvent(): void
    {
        $options = new UnauthorizedStrategyOptions();

        $renderer = $this->createMock(TemplateRendererInterface::class);
        $renderer->expects($this->once())->method('render')
            ->with('error::403')
            ->willReturn('foo');

        $unauthorizedStrategy = new UnauthorizedStrategy(
            $options,
            $renderer
        );
        $event = new Event();
        $event->setParam('request', $this->createMock(RequestInterface::class));
        self::assertInstanceOf(HtmlResponse::class, $unauthorizedStrategy->onUnAuthorized($event));

    }

}
