<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Http\Exception;

class UnauthorizedException extends HttpException
{
    protected int $statusCode = 401;
}
