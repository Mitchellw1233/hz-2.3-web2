<?php

namespace Slimfony\EventDispatcher;

class Event implements StoppableEventInterface
{
    private bool $propagationStopped = false;

    public function isPropagationStopped(): bool
    {
        return $this->propagationStopped;
    }

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * When multiple listeners are on this event, calling this will stop future listeners to execute.
     */
    public function stopPropagation(): void
    {
        $this->propagationStopped = true;
    }
}
