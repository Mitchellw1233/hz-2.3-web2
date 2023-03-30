<?php

namespace Slimfony\EventDispatcher;

interface EventSubscriberInterface
{
    /**
     * Format examples:
     * * ['eventName' => [                  <br>
     *      ['methodName', $priority],      <br>
     *   ]]
     * * ['eventName' => [                  <br>
     *      ['methodName', $priority],      <br>
     *      ['methodName2'],                <br>
     *      ['methodName3', $priority3],    <br>
     *   ]]
     *
     * @return array<string, array<int, array{0: string, 1: int}>>
     */
    public static function getSubscribedEvents(): array;
}
