<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;
use Slimfony\HttpKernel\KernelInterface;

class ResponseEvent extends KernelEvent
{
    private Response $response;

    public function __construct(
        KernelInterface $kernel,
        Request $request,

        Response $response,
    ) {
        parent::__construct($kernel, $request);
        $this->response = $response;
    }

    public function setResponse(Response $response)
    {
        $this->response = $response;
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}
