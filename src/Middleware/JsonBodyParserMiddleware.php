<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Middleware;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface;

class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(
        ServerRequestInterface $request,
        RequestHandlerInterface $handler
    ): ResponseInterface {
        $contentType = strtolower($request->getHeaderLine('Content-Type'));

        if ($this->isJsonMediaType($contentType)) {
            $body = (string) $request->getBody();

            if ($body !== '') {
                $parsed = json_decode($body, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($parsed)) {
                    $request = $request->withParsedBody($parsed);
                }
            }
        }

        return $handler->handle($request);
    }

    private function isJsonMediaType(string $contentType): bool
    {
        [$type] = explode(';', $contentType, 2);
        $type = trim($type);

        return $type === 'application/json'
            || str_ends_with($type, '+json');
    }
}
