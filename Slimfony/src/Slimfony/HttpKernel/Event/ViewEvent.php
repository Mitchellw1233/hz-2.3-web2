<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\KernelInterface;

class ViewEvent extends KernelEvent
{
    public function __construct(
        KernelInterface $kernel,
        Request $request,
        protected mixed $controllerResult,
        protected $response = null
    ) {
        parent::__construct($kernel, $request);
    }

    public function getControllerResult(): mixed
    {
        return $this->controllerResult;
    }

    public function getResponse(): Response|null
    {
        return $this->response;
    }

    public function setResponse(Response $response): void
    {
        $this->response = $response;
    }
}
