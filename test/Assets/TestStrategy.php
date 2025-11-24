<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Assets;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\EventManager\Event;
use Lmc\Rbac\Mezzio\Strategy\AbstractStrategy;
use Override;
use Psr\Http\Message\ResponseInterface;

final class TestStrategy extends AbstractStrategy
{
    #[Override]
    public function onUnAuthorized(Event $event): null|ResponseInterface
    {
        return new HtmlResponse('foo');
    }
}
