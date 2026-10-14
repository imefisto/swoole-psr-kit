<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Testing;

use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;

/**
 * Immutable builder for test requests. Every method returns a new builder,
 * so a configured base builder can be shared between tests.
 *
 * The method shortcuts (get(), post(), ...) only change method and URI, so
 * headers and attributes set before them are kept.
 *
 * withJson() sets the body and Content-Type but not the parsed body:
 * JsonBodyParserMiddleware fills it, as on the server.
 */
class RequestBuilder
{
    private ServerRequestInterface $request;

    public function __construct(
        ServerRequestFactoryInterface $requestFactory,
        private readonly StreamFactoryInterface $streamFactory
    ) {
        $this->request = $requestFactory->createServerRequest('GET', '/');
    }

    public function get(string $uri): static
    {
        return $this->request('GET', $uri);
    }

    public function post(string $uri): static
    {
        return $this->request('POST', $uri);
    }

    public function put(string $uri): static
    {
        return $this->request('PUT', $uri);
    }

    public function patch(string $uri): static
    {
        return $this->request('PATCH', $uri);
    }

    public function delete(string $uri): static
    {
        return $this->request('DELETE', $uri);
    }

    public function request(string $method, string $uri): static
    {
        [$path, $query] = array_pad(explode('?', $uri, 2), 2, '');
        $request = $this->request->withMethod($method);
        $request = $request->withUri($request->getUri()->withPath($path));

        return $this->with($request)->withQueryString($query);
    }

    public function withHeader(string $name, string|array $value): static
    {
        return $this->with($this->request->withHeader($name, $value));
    }

    public function withQuery(array $params): static
    {
        return $this->withQueryString(http_build_query($params));
    }

    public function withJson(mixed $data): static
    {
        $body = $this->streamFactory->createStream(
            json_encode($data, JSON_THROW_ON_ERROR)
        );

        return $this->with(
            $this->request
                ->withHeader('Content-Type', 'application/json')
                ->withBody($body)
        );
    }

    public function withAttribute(string $name, mixed $value): static
    {
        return $this->with($this->request->withAttribute($name, $value));
    }

    public function build(): ServerRequestInterface
    {
        return $this->request;
    }

    private function withQueryString(string $query): static
    {
        parse_str($query, $params);

        return $this->with(
            $this->request
                ->withUri($this->request->getUri()->withQuery($query))
                ->withQueryParams($params)
        );
    }

    private function with(ServerRequestInterface $request): static
    {
        $clone = clone $this;
        $clone->request = $request;
        return $clone;
    }
}
