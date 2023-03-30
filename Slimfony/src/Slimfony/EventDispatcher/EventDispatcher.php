<?php

namespace Slimfony\EventDispatcher;

class EventDispatcher
{
    /**
     * All listeners sorted by event
     *
     * @var array<string, array<int, callable>>
     */
    protected array $sortedListeners;

    /**
     * All listeners
     *
     * @var array<int, callable>
     */
    protected array $listeners;

    public function dispatch(object $event): void
    {
        $eventName = $event::class;

        foreach ($this->listeners[$eventName] as $listener) {
            $listener($event);
        }
    }

    public function addSubscriber(EventSubscriberInterface $subscriber): void
    {
        $se = $subscriber::getSubscribedEvents();

        foreach ($se as $eventName => $listeners) {
            foreach ($listeners as $listener) {
                $this->addListener($eventName, [$subscriber, $listener[0]], $listener[1] ?? 0);
            }
        }
    }

    public function removeSubscriber(EventSubscriberInterface $subscriber): void
    {
        $se = $subscriber::getSubscribedEvents();

        foreach ($se as $listeners) {
            foreach ($listeners as $listener) {
                $this->removeListener([$subscriber, $listener[0]]);
            }
        }
    }

    public function addListener(string $eventName, callable $listener, ?int $priority): void
    {
        $this->sortedListeners[$eventName][$priority ?? 0] = $listener;
        $this->listeners[] = $listener;

        krsort($this->sortedListeners[$eventName]);
    }

    public function removeListener(callable $listener): void
    {
        foreach ($this->listeners as $k => $l) {
            if ($l === $listener) {
                unset($this->listeners[$k]);
                break;
            }
        }

        foreach ($this->sortedListeners as $eventName => $listeners) {
            foreach ($listeners as $p => $l) {
                if ($l === $listener) {
                    unset($this->sortedListeners[$eventName][$p]);
                    return;
                }
            }
        }
    }

    /**
     * @param string|null $eventName
     *
     * @return array<int, callable>
     */
    public function getListeners(string $eventName = null): array
    {
        return $eventName ? $this->sortedListeners[$eventName] : $this->listeners;
    }

    public function hasListeners(string $eventName = null): bool
    {
        return !empty($eventName ? $this->sortedListeners[$eventName] : $this->listeners);
    }
}
