<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Routing;

use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class Route
{
    private array $middlewares = [];
    public readonly string|\Closure|RequestHandlerInterface $handler;

    public function __construct(
        public readonly string $method,
        public readonly string $path,
        string|callable|RequestHandlerInterface $handler
    ) {
        $this->handler = is_callable($handler)
            ? \Closure::fromCallable($handler)
            : $handler;
    }

    public function middleware(MiddlewareInterface ...$middlewares): self
    {
        $this->middlewares = array_merge($this->middlewares, $middlewares);
        return $this;
    }

    public function getMiddlewares(): array
    {
        return $this->middlewares;
    }
}
