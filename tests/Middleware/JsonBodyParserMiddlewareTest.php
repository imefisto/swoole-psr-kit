<?php

declare(strict_types=1);

use Imefisto\SwooleKit\Middleware\JsonBodyParserMiddleware;
use Nyholm\Psr7\Response;
use Nyholm\Psr7\ServerRequest;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

/**
 * @covers Imefisto\SwooleKit\Middleware\JsonBodyParserMiddleware
 */
class JsonBodyParserMiddlewareTest extends TestCase
{
    public function testParsesValidJson()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'application/json'],
            json_encode(['foo' => 'bar'])
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertSame(['foo' => 'bar'], $handler->capturedRequest->getParsedBody());
    }

    public function testParsesValidJsonWithCharset()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'application/json; charset=utf-8'],
            json_encode(['alpha' => 'beta'])
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertSame(['alpha' => 'beta'], $handler->capturedRequest->getParsedBody());
    }

    public function testParsesVendorSpecificJson()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'application/vnd.api+json'],
            json_encode(['vendor' => 'data'])
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertSame(['vendor' => 'data'], $handler->capturedRequest->getParsedBody());
    }

    public function testInvalidJsonLeavesParsedBodyNull()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'application/json'],
            '{invalid json}'
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertNull($handler->capturedRequest->getParsedBody());
    }

    public function testNonJsonContentTypeLeavesParsedBodyNull()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'application/x-www-form-urlencoded'],
            'foo=bar'
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertNull($handler->capturedRequest->getParsedBody());
    }

    public function testParsesJsonWithUppercaseContentType()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'Application/JSON'],
            json_encode(['upper' => 'case'])
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertSame(['upper' => 'case'], $handler->capturedRequest->getParsedBody());
    }

    public function testParsesVendorJsonWithUppercaseContentType()
    {
        $middleware = new JsonBodyParserMiddleware();

        $request = new ServerRequest(
            'POST',
            '/test',
            ['Content-Type' => 'APPLICATION/vnd.api+json'],
            json_encode(['upper' => 'vendor'])
        );

        $handler = new class implements RequestHandlerInterface {
            public ServerRequestInterface $capturedRequest;
            public function handle(ServerRequestInterface $request): ResponseInterface
            {
                $this->capturedRequest = $request;
                return new Response();
            }
        };

        $middleware->process($request, $handler);

        $this->assertSame(['upper' => 'vendor'], $handler->capturedRequest->getParsedBody());
    }
}
