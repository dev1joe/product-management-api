<?php
declare(strict_types=1);

namespace App\Exceptions;


class MissingQueryParamsException extends \RuntimeException
{
    public function __construct(string $parameter, int $code = 0, ?\Throwable $previous = null)
    {
        $message = "missing parameter\"{$parameter}\"";

        parent::__construct($message, $code, $previous);
    }
}