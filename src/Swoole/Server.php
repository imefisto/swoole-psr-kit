<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole;

use Imefisto\SwooleKit\Swoole\Handler\SwooleHandlerInterface;
use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Imefisto\PsrSwoole\PsrRequestFactory;
use Imefisto\PsrSwoole\ResponseMerger;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleServer;

class Server
{
    private SwooleServer $server;

    public function __construct(
        private readonly SwooleHandlerInterface $handler,
        private readonly  ?TableRegistryInterface $tableRegistry = null
    ) {
        $this->server = new SwooleServer('0.0.0.0', 8080);
    }

    public function run(): void
    {
        if ($this->tableRegistry) {
            $this->handler->setTableRegistry($this->tableRegistry);
        }

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
