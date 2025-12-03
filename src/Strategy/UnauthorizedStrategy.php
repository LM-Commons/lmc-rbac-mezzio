<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Laminas\Diactoros\Response\HtmlResponse;
use Laminas\EventManager\Event;
use Lmc\Rbac\Mezzio\Options\UnauthorizedStrategyOptions;
use Mezzio\Template\TemplateRendererInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class UnauthorizedStrategy extends AbstractStrategy
{
    public function __construct(
        private UnauthorizedStrategyOptions $options,
        private TemplateRendererInterface $renderer,
    ) {
    }

    public function onUnAuthorized(Event $event): null|ResponseInterface
    {
        if ($event->getParam('request') instanceof RequestInterface) {
            return new HtmlResponse(
                $this->renderer->render(
                    $this->options->getTemplate(),
                    [
                        'error' => $event->getParam('error'),
                    ],
                )
            );
        }
        return null;
    }
}
