<?php

declare(strict_types=1);

namespace Lmc\Rbac\Mezzio\Strategy;

use Laminas\EventManager\AbstractListenerAggregate;
use Laminas\EventManager\Event;
use Laminas\EventManager\EventManagerInterface;
use Lmc\Rbac\Mezzio\Event\AuthorizationEvent;
use Psr\Http\Message\ResponseInterface;

abstract class AbstractStrategy extends AbstractListenerAggregate
{
    /**
     * @inheritDoc
     */
    public function attach(EventManagerInterface $events, $priority = 1): void
    {
        $this->listeners[] = $events->attach(
            AuthorizationEvent::EVENT_UNAUTHORIZED,
            [
                $this,
                'onUnAuthorized',
            ],
            $priority
        );
    }

    abstract public function onUnAuthorized(Event $event): null|ResponseInterface;
}
