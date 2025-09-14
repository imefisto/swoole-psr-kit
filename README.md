# SwooleKit

[![Latest Version on Packagist(ToDo)](https://img.shields.io/packagist/v/imefisto/swoole-kit.svg?style=flat-square)](https://packagist.org/packages/imefisto/swoole-kit)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A PHP library providing clean interfaces and handlers for Swoole server implementation, with support for HTTP, WebSocket, and Worker management.

> ⚠️ **Note**: This project is currently in beta/experimental stage. API may change without notice.

## Requirements

- PHP 8.3+
- Swoole 5.1+

## Installation

```bash
composer require imefisto/swoole-kit
```

## Features

- HTTP request handling with PSR-7 compliance
- WebSocket server support with clean event handlers
- Worker management interface
- Middleware support
- Extensible handler system

## Basic Usage

### HTTP Server

```php
use Imefisto\SwooleKit\Swoole\Handler\DefaultSwooleHandler;
use Imefisto\SwooleKit\Swoole\Handler\HttpHandlerInterface;
use Imefisto\SwooleKit\Swoole\Server;
use Swoole\Http\Request;
use Swoole\Http\Response;

class MyHttpHandler implements HttpHandlerInterface 
{
    public function onRequest(Request $request, Response $response): void 
    {
        $response->end("Hello World!");
    }
}

$handler = new DefaultSwooleHandler();
$handler->setHttpHandler(new MyHttpHandler());

$server = new Server($handler);
$server->run();
```

### WebSocket Server

```php
use Imefisto\SwooleKit\Swoole\Handler\DefaultSwooleHandler;
use Imefisto\SwooleKit\Swoole\Handler\WebSocketHandlerInterface;
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;
use Swoole\Http\Request;

class MyWebSocketHandler implements WebSocketHandlerInterface 
{
    public function onOpen(Server $server, Request $request): void 
    {
        echo "Connection open: {$request->fd}\n";
    }

    public function onMessage(Server $server, Frame $frame): void 
    {
        $server->push($frame->fd, "Received: {$frame->data}");
    }

    public function onClose(Server $server, int $fd): void 
    {
        echo "Connection closed: {$fd}\n";
    }

    public function onDisconnect(Server $server, int $fd): void 
    {
        echo "Client disconnected: {$fd}\n";
    }
}

$handler = new DefaultSwooleHandler();
$handler->setWebSocketHandler(new MyWebSocketHandler());

$server = new Swoole\WebSocket\Server('127.0.0.1', 8080);
$server->on('open', [$handler, 'onOpen']);
$server->on('message', [$handler, 'onMessage']);
$server->on('close', [$handler, 'onClose']);
$server->start();
```

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
