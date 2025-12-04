<?php

declare(strict_types=1);

namespace LmcTest\Rbac\Mezzio\Assets;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\EventManager\EventManagerAwareInterface;
use Laminas\EventManager\EventManagerAwareTrait;
use Override;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class TestUnauthorizedHandler implements MiddlewareInterface, EventManagerAwareInterface
{
    use EventManagerAwareTrait;

    #[Override]
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        return new HtmlResponse('foo');
    }
}
