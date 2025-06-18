<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Server as SwooleBaseServer;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleWebSocketServer;

class DefaultSwooleHandler implements SwooleHandlerInterface
{
    private ?HttpHandlerInterface $httpHandler = null;
    private ?WebSocketHandlerInterface $webSocketHandler = null;
    private ?WorkerHandlerInterface $workerHandler = null;

    public function setHttpHandler(HttpHandlerInterface $handler): void
    {
        $this->httpHandler = $handler;
    }

    public function setWebSocketHandler(WebSocketHandlerInterface $handler): void
    {
        $this->webSocketHandler = $handler;
    }

    public function setWorkerHandler(WorkerHandlerInterface $handler): void
    {
        $this->workerHandler = $handler;
    }

    public function onStart(SwooleBaseServer $server): void
    {
        // Default empty implementation
    }

    public function onRequest(Request $request, Response $response): void
    {
        if ($this->httpHandler === null) {
            $response->status(500);
            $response->end('HTTP handler not configured');
            return;
        }

        $this->httpHandler->onRequest($request, $response);
    }

    public function onOpen(SwooleWebSocketServer $server, Request $request): void
    {
        if ($this->webSocketHandler === null) {
            return;
        }

        $this->webSocketHandler->onOpen($server, $request);
    }

    public function onMessage(SwooleWebSocketServer $server, Frame $frame): void
    {
        if ($this->webSocketHandler === null) {
            return;
        }

        $this->webSocketHandler->onMessage($server, $frame);
    }

    public function onDisconnect(SwooleWebSocketServer $server, int $fd): void
    {
        if ($this->webSocketHandler === null) {
            return;
        }

        $this->webSocketHandler->onDisconnect($server, $fd);
    }

    public function onClose(SwooleWebSocketServer $server, int $fd): void
    {
        if ($this->webSocketHandler === null) {
            return;
        }

        $this->webSocketHandler->onClose($server, $fd);
    }

    public function onWorkerStart(SwooleBaseServer $server, int $workerId): void
    {
        if ($this->workerHandler === null) {
            return;
        }

        $this->workerHandler->onWorkerStart($server, $workerId);
    }
}
