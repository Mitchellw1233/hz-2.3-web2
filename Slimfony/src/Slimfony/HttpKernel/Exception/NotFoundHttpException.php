<?php

namespace Slimfony\HttpKernel\Exception;

class NotFoundHttpException extends HttpException
{
    public function __construct(string $message = '', int $code = 0, ?\Throwable $previous = null)
    {
        $message ??= 'Page not found';
        parent::__construct(404, $message, $code, $previous);
    }
}
