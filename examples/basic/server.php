<?php

require __DIR__ . '/../../vendor/autoload.php';

use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UploadedFileFactory;
use Http\Factory\Guzzle\UriFactory;
use Imefisto\SwooleKit\DependencyInjection\ContainerFactory;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Swoole\Server;
use Imefisto\SwooleKit\Swoole\Handler\DefaultSwooleHandler;
use Imefisto\SwooleKit\Swoole\Handler\SwooleHandlerInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use function DI\autowire;
use function DI\create;
use function DI\get;

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
            'status' => 'Hello!',
        ];
        
        $response->getBody()->write(json_encode($body));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

$config = [];

$routes = [
    ['GET', '/example', Example::class],
];

$dependencies = [
    ResponseFactoryInterface::class => autowire(Psr17Factory::class),
    StreamFactoryInterface::class => autowire(Psr17Factory::class),
    UploadedFileFactoryInterface::class => autowire(Psr17Factory::class),
    UriFactoryInterface::class => autowire(Psr17Factory::class),
    Router::class => function ($container) {
        return new Router($container, $container->get('routes'));
    },
    SwooleHandlerInterface::class => autowire(DefaultSwooleHandler::class)
];

$container = ContainerFactory::create($config, $dependencies, $routes);

$server = $container->get(Server::class);
$server->run();
