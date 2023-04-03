<?php

namespace Slimfony\HttpKernel\EventListener;

use Slimfony\EventDispatcher\EventSubscriberInterface;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\Event\ViewEvent;

class TypeResponseListener implements EventSubscriberInterface
{
    public function onView(ViewEvent $event): void
    {
        $result = $event->getControllerResult();

        if (is_string($result)) {
            $event->setResponse(new Response($result));
        }
    }

    public static function getSubscribedEvents(): array
    {
        return [
            ViewEvent::class => [
                ['onView'],
            ],
        ];
    }
}
