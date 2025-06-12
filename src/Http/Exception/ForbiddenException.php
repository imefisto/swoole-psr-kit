<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Http\Exception;

class ForbiddenException extends HttpException
{
    protected int $statusCode = 403;
}
