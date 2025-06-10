<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Imefisto\PsrSwoole\PsrRequestFactory;
use Imefisto\PsrSwoole\ResponseMerger;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server as SwooleServer;

class DefaultSwooleHandler implements SwooleHandlerInterface
{
    private ?TableRegistryInterface $tableRegistry = null;

    public function __construct(
        private readonly Router $router,
        private readonly PsrRequestFactory $psrRequestFactory,
        private readonly ResponseMerger $responseMerger,
    ) {
    }

    public function setTableRegistry(?TableRegistryInterface $tableRegistry): void
    {
        $this->tableRegistry = $tableRegistry;
    }

    public function onStart(SwooleServer $server): void
    {
        // Default empty implementation
    }

    public function onRequest(Request $request, Response $response): void
    {
        $psrRequest = $this->convertSwooleRequestToPsr7($request);
        $psrRequest = $psrRequest->withAttribute('tables', $this->tableRegistry);
        $psrResponse = $this->router->dispatch($psrRequest);
        $this->sendPsr7ResponseToSwoole($psrResponse, $response);
    }

    protected function convertSwooleRequestToPsr7(
        Request $request
    ): ServerRequestInterface {
        return $this->psrRequestFactory->createServerRequest($request);
    }

    protected function sendPsr7ResponseToSwoole(
        ResponseInterface $psrResponse,
        Response $swooleResponse
    ): Response {
        return $this->responseMerger->toSwoole($psrResponse, $swooleResponse);
    }

    public function onOpen(SwooleServer $server, Request $request): void
    {
        // Default empty implementation
    }

    public function onMessage(SwooleServer $server, Frame $frame): void
    {
        // Default empty implementation
    }

    public function onDisconnect(SwooleServer $server, int $fd): void
    {
        // Default empty implementation
    }

    public function onClose(SwooleServer $server, int $fd): void
    {
        // Default empty implementation
    }

    public function onWorkerStart(SwooleServer $server, int $workerId): void
    {
        // Default empty implementation
    }
}
