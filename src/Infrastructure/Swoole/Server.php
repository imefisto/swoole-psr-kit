<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Infrastructure\Swoole;

use Imefisto\SwooleKit\Infrastructure\Routing\Router;
use Imefisto\PsrSwoole\ServerRequest as PsrRequest;
use Imefisto\PsrSwoole\ResponseMerger;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleServer;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

class Server
{
    private SwooleServer $server;
    private Router $router;
    private ContainerInterface $container;
    private ResponseMerger $responseMerger;

    public function __construct(ContainerInterface $container, Router $router)
    {
        $this->container = $container;
        $this->router = $router;
        $this->server = new SwooleServer('0.0.0.0', 8080);
        $this->responseMerger = new ResponseMerger();
    }

    public function run(): void
    {
        $this->server->on(
            'start', function (SwooleServer $server) {
                echo "Swoole http server is started at http://0.0.0.0:8080\n";
            }
        );

        $this->server->on(
            'request',
            function (Request $request, Response $response) {
                $psrRequest = $this->convertSwooleRequestToPsr7($request);
                $psrResponse = $this->router->dispatch($psrRequest);
                $this->sendPsr7ResponseToSwoole($psrResponse, $response);
            }
        );

        $this->server->on(
            'open',
            function (SwooleServer $server, Request $request) {
            }
        );

        $this->server->on(
            'message',
            function (SwooleServer $server, Frame $frame) {
            }
        );

        $this->server->on(
            'disconnect',
            function (SwooleServer $server, int $fd) {
            }
        );

        $this->server->on(
            'close',
            function (SwooleServer $server, int $fd) {
            }
        );

        $this->server->start();
    }

    private function convertSwooleRequestToPsr7(
        Request $request
    ): ServerRequestInterface {
        return new PsrRequest(
            $request,
            $this->container->get(UriFactoryInterface::class),
            $this->container->get(StreamFactoryInterface::class),
            $this->container->get(UploadedFileFactoryInterface::class)
        );
    }

    private function sendPsr7ResponseToSwoole(
        ResponseInterface $psrResponse,
        Response $swooleResponse
    ): Response {
        return $this->responseMerger->toSwoole($psrResponse, $swooleResponse);
    }
}
