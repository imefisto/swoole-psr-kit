<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\Http\Request;
use Swoole\Http\Response;

interface HttpHandlerInterface
{
    public function onRequest(Request $request, Response $response): void;
}
