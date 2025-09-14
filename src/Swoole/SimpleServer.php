<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole;

use Imefisto\SwooleKit\Swoole\Handler\SwooleHandlerInterface;

class SimpleServer
{
    private \Swoole\Server $server;

    public function __construct(
        private readonly SwooleHandlerInterface $handler,
        \Swoole\Server $server = null
    ) {
        $this->server = $server ?? new \Swoole\Http\Server('0.0.0.0', 8080);
    }

    public function run(): void
    {
        $this->server->on('start', $this->handler->onStart(...));
        $this->server->on('request', $this->handler->onRequest(...));
        $this->server->on('open', $this->handler->onOpen(...));
        $this->server->on('message', $this->handler->onMessage(...));
        $this->server->on('disconnect', $this->handler->onDisconnect(...));
        $this->server->on('close', $this->handler->onClose(...));
        $this->server->on('workerStart', $this->handler->onWorkerStart(...));

        $this->server->start();
    }
}
