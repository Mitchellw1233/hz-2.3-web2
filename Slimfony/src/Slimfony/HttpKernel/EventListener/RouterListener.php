<?php

namespace Slimfony\HttpKernel\EventListener;

use Slimfony\EventDispatcher\EventSubscriberInterface;
use Slimfony\HttpKernel\Event\RequestEvent;
use Slimfony\Routing\RouteResolver;

class RouterListener implements EventSubscriberInterface
{
    public function __construct(
        protected RouteResolver $rr
    )
    {
    }

    public function onRequest(RequestEvent $event): void
    {
        $event->getRequest()->getAttributes()->set(
            '_route',
            $this->rr->resolveRoute($event->getRequest(), $this->rr->resolveRoutes())
        );
    }

    public static function getSubscribedEvents(): array
    {
        return [
            RequestEvent::class => [
                ['onRequest', 255],
            ],
        ];
    }
}
