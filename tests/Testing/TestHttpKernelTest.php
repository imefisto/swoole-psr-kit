<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Tests\Testing;

use DI\Container;
use Imefisto\SwooleKit\Http\Exception\ForbiddenException;
use Imefisto\SwooleKit\Routing\Route;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Testing\TestHttpKernel;
use Nyholm\Psr7\Factory\Psr17Factory;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Imefisto\SwooleKit\Testing\TestHttpKernel
 */
class TestHttpKernelTest extends TestCase
{
    public function testReturnsControllerResponse()
    {
        $kernel = $this->createKernel(
            new Route('GET', '/hello', fn () => new Response(200, [], 'Hello!'))
        );

        $response = $kernel->handle(new ServerRequest('GET', '/hello'));

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('Hello!', (string) $response->getBody());
    }

    public function testUnknownRouteReturns404()
    {
        $kernel = $this->createKernel(
            new Route('GET', '/hello', fn () => new Response())
        );

        $response = $kernel->handle(new ServerRequest('GET', '/missing'));

        $this->assertSame(404, $response->getStatusCode());
    }

    public function testWrongMethodReturns405WithAllowHeader()
    {
        $kernel = $this->createKernel(
            new Route('GET', '/hello', fn () => new Response())
        );

        $response = $kernel->handle(new ServerRequest('POST', '/hello'));

        $this->assertSame(405, $response->getStatusCode());
        $this->assertSame('GET', $response->getHeaderLine('Allow'));
    }

    public function testHttpExceptionIsMappedToItsStatusCode()
    {
        $kernel = $this->createKernel(
            new Route(
                'GET',
                '/admin',
                fn () => throw new ForbiddenException('Forbidden')
            )
        );

        $response = $kernel->handle(new ServerRequest('GET', '/admin'));

        $this->assertSame(403, $response->getStatusCode());
        $this->assertSame('Forbidden', (string) $response->getBody());
    }

    public function testUnexpectedExceptionIsRethrown()
    {
        $kernel = $this->createKernel(
            new Route(
                'GET',
                '/boom',
                fn () => throw new \LogicException('boom')
            )
        );

        $this->expectException(\LogicException::class);
        $this->expectExceptionMessage('boom');

        $kernel->handle(new ServerRequest('GET', '/boom'));
    }

    private function createKernel(Route $route): TestHttpKernel
    {
        return new TestHttpKernel(
            new Router(new Container(), [$route]),
            new Psr17Factory()
        );
    }
}
