<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Swoole\Handler;

use Imefisto\PsrSwoole\PsrRequestFactory;
use Imefisto\PsrSwoole\ResponseMerger;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use League\Route\Http\Exception as LeagueException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;

class DefaultHttpHandler implements HttpHandlerInterface
{
    private ?LoggerInterface $logger = null;
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

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function onRequest(Request $request, Response $response): void
    {
        try {
            $psrRequest = $this->convertSwooleRequestToPsr7($request);

            if (!is_null($this->tableRegistry)) {
                $psrRequest = $psrRequest->withAttribute(
                    'tables',
                    $this->tableRegistry
                );
            }

            $psrResponse = $this->router->dispatch($psrRequest);
            $this->sendPsr7ResponseToSwoole($psrResponse, $response);
        } catch (LeagueException $e) {
            $response->status($e->getStatusCode());
            $response->end($e->getMessage());
        } catch (\Throwable $e) {
            $this->logException($e);
            $response->status(500);
            $response->end('Internal Server Error');
        }
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

    private function logException(\Throwable $e): void
    {
        if (is_null($this->logger)) {
            return;
        }

        $this->logger->error(
            $e->getMessage(),
            [ 'file' => $e->getFile(), 'line' => $e->getLine() ]
        );
    }
}
