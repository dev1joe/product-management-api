<?php
declare(strict_types=1);

namespace App\Exceptions;

class MethodNotImplementedException extends \RuntimeException
{
    public function __construct(
        string $message = "Method Not Implemented Yet", int $code = 0, ?Throwable $previous = null
    ){
        parent::__construct($message, $code, $previous);
    }
}