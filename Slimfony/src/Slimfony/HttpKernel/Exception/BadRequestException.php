<?php

namespace Slimfony\HttpKernel\Exception;

class BadRequestException extends HttpException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $message ??= 'Bad request';
        parent::__construct(400, $message, $code, $previous);
    }
}
