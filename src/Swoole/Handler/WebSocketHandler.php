<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;
use Swoole\Http\Request;

interface WebSocketHandler
{
    public function onOpen(Server $server, Request $request): void;
    public function onClose(Server $server, int $fd): void;
    public function onMessage(Server $server, Frame $frame): void;
}
