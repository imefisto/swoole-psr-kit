<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Tests\Swoole\Handler;

use DI\Container;
use Imefisto\PsrSwoole\PsrRequestFactory;
use Imefisto\PsrSwoole\ResponseMerger;
use Imefisto\SwooleKit\Http\Exception\UnauthorizedException;
use Imefisto\SwooleKit\Routing\Route;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Swoole\Handler\DefaultHttpHandler;
use Nyholm\Psr7\Factory\Psr17Factory;
use PHPUnit\Framework\TestCase;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 * @covers \Imefisto\SwooleKit\Swoole\Handler\DefaultHttpHandler
 */
class DefaultHttpHandlerTest extends TestCase
{
    public function testHttpExceptionIsMappedToItsStatusCode()
    {
        $route = new Route(
            'GET',
            '/private',
            fn () => throw new UnauthorizedException('Unauthorized')
        );

        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('status')->with(401);
        $response->expects($this->once())->method('end')->with('Unauthorized');

        $this->dispatch('GET', '/private', $route, $response);
    }

    public function testLeagueExceptionHeadersAreCopied()
    {
        $route = new Route('GET', '/hello', fn () => null);

        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('status')->with(405);
        $response->expects($this->once())->method('header')->with('Allow', 'GET');
        $response->expects($this->once())->method('end');

        $this->dispatch('POST', '/hello', $route, $response);
    }

    public function testUnexpectedExceptionIsMappedTo500()
    {
        $route = new Route(
            'GET',
            '/boom',
            fn () => throw new \RuntimeException('boom')
        );

        $response = $this->createMock(Response::class);
        $response->expects($this->once())->method('status')->with(500);
        $response->expects($this->once())
            ->method('end')
            ->with('Internal Server Error');

        $this->dispatch('GET', '/boom', $route, $response);
    }

    private function dispatch(
        string $method,
        string $uri,
        Route $route,
        Response $response
    ): void {
        $request = new Request();
        $request->server = ['request_method' => $method, 'request_uri' => $uri];
        $request->header = ['host' => 'localhost'];

        $psr17Factory = new Psr17Factory();
        $handler = new DefaultHttpHandler(
            new Router(new Container(), [$route]),
            new PsrRequestFactory($psr17Factory, $psr17Factory, $psr17Factory),
            new ResponseMerger()
        );

        $handler->onRequest($request, $response);
    }
}
