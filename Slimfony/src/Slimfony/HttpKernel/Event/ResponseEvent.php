<?php

namespace Slimfony\HttpKernel\Event;

use Slimfony\EventDispatcher\Event;
use Slimfony\HttpFoundation\Request;
use Slimfony\HttpFoundation\Response;

class ResponseEvent extends Event
{
    public function __construct(
        private Response $response,
        private Request  $request,
    ) {
    }

    public function getResponse(): Response
    {
        return $this->response;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
