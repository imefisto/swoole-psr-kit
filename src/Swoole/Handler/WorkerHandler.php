<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\Server;

interface WorkerHandler
{
    public function onWorkerStart(Server $server, int $workerId): void;
    public function onWorkerError(Server $server, int $workerId, int $workerPid, int $exitCode, int $signal): void;
    public function onWorkerExit(Server $server, int $workerId);
}
