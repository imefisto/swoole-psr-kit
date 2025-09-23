<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Swoole\Server;

class DefaultServerLifecycleHandler implements ServerLifecycleHandler
{
    public function onStart(Server $server): void
    {
    }

    public function onBeforeShutdown(Server $server): void
    {
    }

    public function onShutdown(Server $server): void
    {
    }
}

