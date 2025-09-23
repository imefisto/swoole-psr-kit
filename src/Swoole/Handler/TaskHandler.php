<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\Server;
use Swoole\Server\Task;

interface TaskHandler
{
    public function onTask(Server $server, Task $task): void;
    public function onFinish(Server $server, int $taskId, mixed $data): void;
}
