<?php

namespace Slimfony\HttpFoundation\Bag;

class ResponseHeaderBag extends HeaderBag
{
    protected array $headerNames = [];
    public function __construct(array $headers = [])
    {
        parent::__construct($headers);

        if (!isset($this->data['cache-control'])) {
            $this->set('Cache-Control', '');
        }

        if (!isset($this->data['headers'])) {
            $this->set('Date', gmdate('D, d M Y H:i:s').' GMT');
        }
    }
}