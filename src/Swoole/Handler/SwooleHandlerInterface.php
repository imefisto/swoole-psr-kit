<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server as SwooleBaseServer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

interface SwooleHandlerInterface
{
    public function onStart(SwooleBaseServer $server): void;

    public function onRequest(Request $request, Response $response): void;

    public function onOpen(SwooleWebSocketServer $server, Request $request): void;

    public function onMessage(SwooleWebSocketServer $server, Frame $frame): void;

    public function onDisconnect(SwooleWebSocketServer $server, int $fd): void;

    public function onClose(SwooleWebSocketServer $server, int $fd): void;

    public function onWorkerStart(SwooleBaseServer $server, int $workerId): void;
}
