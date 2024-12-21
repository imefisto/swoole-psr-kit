<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Presentation\Controller;

use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Example
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function __invoke(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);
        $body = [
            'status' => 'success',
        ];
        
        $response->getBody()->write(json_encode($body));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
