<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole;

use Imefisto\SwooleKit\Swoole\Handler\HttpHandler;
use Imefisto\SwooleKit\Swoole\Handler\WebSocketHandler;
use Imefisto\SwooleKit\Swoole\Handler\WorkerHandler;
use Imefisto\SwooleKit\Swoole\Handler\TaskHandler;
use Imefisto\SwooleKit\Swoole\Handler\ServerLifecycleHandler;

class SimpleServer
{
    private \Swoole\Server $server;

    public function __construct(
        private readonly ?HttpHandler $httpHandler = null,
        private readonly ?WebSocketHandler $webSocketHandler = null,
        private readonly ?WorkerHandler $workerHandler = null,
        private readonly ?TaskHandler $taskHandler = null,
        private readonly ?ServerLifecycleHandler $lifecycleHandler = null,
        ?\Swoole\Server $server = null,
        string $host = '0.0.0.0',
        int $port = 8080
    ) {
        $this->server = $server ?? $this->createServer($host, $port);
    }

    public function run(): void
    {
        if ($this->lifecycleHandler) {
            $this->server->on('start', $this->lifecycleHandler->onStart(...));
            $this->server->on('beforeShutdown', $this->lifecycleHandler->onBeforeShutdown(...));
            $this->server->on('shutdown', $this->lifecycleHandler->onShutdown(...));
        }

        if ($this->httpHandler) {
            $this->server->on('request', $this->httpHandler->onRequest(...));
        }

        if ($this->webSocketHandler) {
            $this->server->on('open', $this->webSocketHandler->onOpen(...));
            $this->server->on('message', $this->webSocketHandler->onMessage(...));
            $this->server->on('close', $this->webSocketHandler->onClose(...));
        }

        if ($this->workerHandler) {
            $this->server->on('workerStart', $this->workerHandler->onWorkerStart(...));
            $this->server->on('workerError', $this->workerHandler->onWorkerError(...));
            $this->server->on('workerExit', $this->workerHandler->onWorkerExit(...));
        }

        if ($this->taskHandler) {
            $this->server->on('task', $this->taskHandler->onTask(...));
            $this->server->on('finish', $this->taskHandler->onFinish(...));
        }

        $this->server->start();
    }

    private function createServer(string $host, int $port): \Swoole\Server
    {
        if ($this->webSocketHandler) {
            return new \Swoole\WebSocket\Server($host, $port);
        }

        return new \Swoole\Http\Server($host, $port);
    }
}
