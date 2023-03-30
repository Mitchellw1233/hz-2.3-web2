<?php

namespace Slimfony\EventDispatcher;

class EventDispatcher
{
    /**
     * @var array<string, array<int, callable>>
     */
    protected array $listeners;

    public function dispatch(object $event, string $name = null): void
    {
        $name ??= $event::class;
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void {
        $s = $subscriber::getSubscribedEvents();
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void {
        $s = $subscriber::getSubscribedEvents();
    }

    public function addListener(callable $listener, ?int $priority): void {

    }

    public function removeListener(callable $listener): void {

    }

    public function getListeners(): array {
        return [];
    }
}
