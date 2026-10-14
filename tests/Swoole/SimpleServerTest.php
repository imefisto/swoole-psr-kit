<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Tests\Swoole;

use Imefisto\SwooleKit\Swoole\Handler\HttpHandler;
use Imefisto\SwooleKit\Swoole\Handler\WebSocketHandler;
use Imefisto\SwooleKit\Swoole\Handler\WorkerHandler;
use Imefisto\SwooleKit\Swoole\SimpleServer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Imefisto\SwooleKit\Swoole\SimpleServer
 */
class SimpleServerTest extends TestCase
{
    public function testWorkerHandlerEventsAreRegistered()
    {
        $server = $this->createMock(\Swoole\Server::class);
        $server->expects($this->exactly(3))
            ->method('on')
            ->withConsecutive(
                ['workerStart', $this->isInstanceOf(\Closure::class)],
                ['workerError', $this->isInstanceOf(\Closure::class)],
                ['workerExit', $this->isInstanceOf(\Closure::class)]
            );
        $server->expects($this->once())->method('start');

        $simpleServer = new SimpleServer(
            workerHandler: $this->createMock(WorkerHandler::class),
            server: $server
        );
        $simpleServer->run();
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testDefaultServerIsHttpServer()
    {
        $simpleServer = new SimpleServer(
            httpHandler: $this->createMock(HttpHandler::class),
            host: '127.0.0.1',
            port: 0
        );

        $server = $this->getServer($simpleServer);
        $this->assertInstanceOf(\Swoole\Http\Server::class, $server);
        $this->assertNotInstanceOf(\Swoole\WebSocket\Server::class, $server);
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testDefaultServerIsWebSocketServerWithWebSocketHandler()
    {
        $simpleServer = new SimpleServer(
            webSocketHandler: $this->createMock(WebSocketHandler::class),
            host: '127.0.0.1',
            port: 0
        );

        $this->assertInstanceOf(
            \Swoole\WebSocket\Server::class,
            $this->getServer($simpleServer)
        );
    }

    /**
     * @runInSeparateProcess
     *
     * @return void
     */
    public function testHostAndPortAreConfigurable()
    {
        $simpleServer = new SimpleServer(host: '127.0.0.1', port: 9501);

        $server = $this->getServer($simpleServer);
        $this->assertSame('127.0.0.1', $server->host);
        $this->assertSame(9501, $server->port);
    }

    private function getServer(SimpleServer $simpleServer): \Swoole\Server
    {
        return (new \ReflectionProperty(SimpleServer::class, 'server'))
            ->getValue($simpleServer);
    }
}
