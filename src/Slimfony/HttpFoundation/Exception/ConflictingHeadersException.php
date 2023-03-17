<?php

namespace Slimfony\HttpFoundation\Exception;

/**
 * The HTTP request contains headers with conflicting information.
 */
class ConflictingHeadersException extends \UnexpectedValueException implements RequestExceptionInterface
{
}
