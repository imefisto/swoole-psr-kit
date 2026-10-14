<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Testing;

use Imefisto\SwooleKit\Http\Exception\HttpException;
use Imefisto\SwooleKit\Routing\Router;
use League\Route\Http\Exception as LeagueException;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Dispatches PSR-7 requests through the router without starting a server.
 *
 * HTTP exceptions are turned into responses the same way DefaultHttpHandler
 * does. Any other exception is rethrown, so tests see the real failure
 * instead of a 500.
 */
class TestHttpKernel
{
    public function __construct(
        private readonly Router $router,
        private readonly ResponseFactoryInterface $responseFactory,
    ) {
    }

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            return $this->router->dispatch($request);
        } catch (LeagueException $e) {
            $response = $this->createErrorResponse(
                $e->getStatusCode(),
                $e->getMessage()
            );
            foreach ($e->getHeaders() as $name => $value) {
                $response = $response->withHeader($name, $value);
            }
            return $response;
        } catch (HttpException $e) {
            return $this->createErrorResponse(
                $e->getStatusCode(),
                $e->getMessage()
            );
        }
    }

    private function createErrorResponse(
        int $status,
        string $message
    ): ResponseInterface {
        $response = $this->responseFactory->createResponse($status);
        $response->getBody()->write($message);
        return $response;
    }
}
