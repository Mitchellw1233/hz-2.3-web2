<?php

namespace Slimfony\HttpKernel\Exception;

use Slimfony\HttpFoundation\Response;

class ForbiddenException extends HttpException
{
    public function __construct($message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $message ??= 'Forbidden';
        parent::__construct(403, $message, $code, $previous);
    }

}
