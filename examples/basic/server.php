<?php

require __DIR__ . '/../../vendor/autoload.php';

use Imefisto\SwooleKit\DependencyInjection\ContainerFactory;
use Imefisto\SwooleKit\Routing\Route;
use Imefisto\SwooleKit\Routing\Router;
use Imefisto\SwooleKit\Swoole\Handler\DefaultHttpHandler;
use Imefisto\SwooleKit\Swoole\Handler\HttpHandler;
use Imefisto\SwooleKit\Swoole\SimpleServer;
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

class MyController
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
    new Route('GET', '/hello', MyController::class)
];

$dependencies = [
    ResponseFactoryInterface::class => autowire(Psr17Factory::class),
    StreamFactoryInterface::class => autowire(Psr17Factory::class),
    UploadedFileFactoryInterface::class => autowire(Psr17Factory::class),
    UriFactoryInterface::class => autowire(Psr17Factory::class),
    Router::class => function ($container) {
        return new Router($container, $container->get('routes'));
    },
    HttpHandler::class => autowire(DefaultHttpHandler::class),
    SimpleServer::class => create()
        ->constructor(
            get(HttpHandler::class)
        ),
];

$container = ContainerFactory::create($config, $dependencies, $routes);

$server = $container->get(SimpleServer::class);
$server->run();
