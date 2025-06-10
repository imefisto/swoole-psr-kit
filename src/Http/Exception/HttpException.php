<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Http\Exception;

abstract class HttpException extends \RuntimeException
{
    protected int $statusCode;

    public function __construct(
        string $message = '', 
        int $code = 0, 
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }
}
