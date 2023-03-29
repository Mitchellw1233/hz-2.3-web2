<?php

namespace Slimfony\EventDispatcher;

interface EventSubscriberInterface
{
    /**
     * Supported formats:
     * * ['eventName' => 'methodName']
     * * ['eventName' => ['methodName', $priority]]
     *
     * @return array<string, string|array{0: string, 1: int}>
     */
    public static function getSubscribedEvents(): array;
}
