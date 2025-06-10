<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleServer;

interface SwooleHandlerInterface
{
    public function setTableRegistry(?TableRegistryInterface $tableRegistry): void;

    public function onStart(SwooleServer $server): void;

    public function onRequest(Request $request, Response $response): void;

    public function onOpen(SwooleServer $server, Request $request): void;

    public function onMessage(SwooleServer $server, Frame $frame): void;

    public function onDisconnect(SwooleServer $server, int $fd): void;

    public function onClose(SwooleServer $server, int $fd): void;

    public function onWorkerStart(SwooleServer $server, int $workerId): void;
}
