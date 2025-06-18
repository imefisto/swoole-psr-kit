<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\WebSocket\Server;

interface WorkerHandlerInterface
{
    public function onWorkerStart(Server $server, int $workerId): void;
}
