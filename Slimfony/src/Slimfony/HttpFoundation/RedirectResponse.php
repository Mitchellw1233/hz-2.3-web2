<?php

namespace Slimfony\HttpFoundation;

class RedirectResponse extends Response
{
    public function __construct(string $location, ?string $content = '', int $status = 302, array $headers = [])
    {
        parent::__construct($content, $status, $headers);
        $this->getHeaders()->set('Location', [$location]);
    }
}