<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\EventDispatcher\Event;
use Slimfony\HttpFoundation\Request;
use Slimfony\HttpKernel\KernelInterface;

class KernelEvent extends Event
{
    public function __construct(
        private KernelInterface $kernel,
        private Request $request,
    ) {
    }

    public function getKernel(): KernelInterface
    {
        return $this->kernel;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}