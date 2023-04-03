<?php

namespace Slimfony\HttpKernel\Exception;

class HttpException extends \RuntimeException
{
    public function __construct(protected int $statusCode, string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
