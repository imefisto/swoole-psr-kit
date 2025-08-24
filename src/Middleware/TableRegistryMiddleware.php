<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Middleware;

use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class TableRegistryMiddleware implements MiddlewareInterface
{
    public function __construct(
        private readonly TableRegistryInterface $tableRegistry,
        private readonly string $attributeName = 'tables'
    ) {
    }

    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        return $handler->handle(
            $request->withAttribute(
                $this->attributeName,
                $this->tableRegistry
            )
        );
    }
}
