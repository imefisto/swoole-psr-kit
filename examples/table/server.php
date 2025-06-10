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
use Imefisto\SwooleKit\Swoole\Table\TableRegistry;
use Imefisto\SwooleKit\Swoole\Table\TableRegistryInterface;
use Nyholm\Psr7\Factory\Psr17Factory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use Swoole\Table;
use function DI\autowire;
use function DI\create;
use function DI\get;

class Example
{
    public function __construct(
        private ResponseFactoryInterface $responseFactory
    ) {
    }

    public function register(
        ServerRequestInterface $request
    ): ResponseInterface {
        $body = $request->getParsedBody();

        if (isset($body['user'])) {
            $tableRegistry = $request->getAttribute('tables');
            $users = $tableRegistry->get('users');
            $users->set($body['user'], ['username' => $body['user']]);
        }

        return $this->responseFactory->createResponse(200);
    }

    public function list(ServerRequestInterface $request): ResponseInterface
    {
        $response = $this->responseFactory->createResponse(200);
        $tableRegistry = $request->getAttribute('tables');
        $userTable = $tableRegistry->get('users');
        $users = [];
        foreach ($userTable as $key => $value) {
            $users[] = $value;
        }

        $body = [
            'users' => $users,
        ];
        
        $response->getBody()->write(json_encode($body));
        return $response->withHeader('Content-Type', 'application/json');
    }
}

$config = [];

$routes = [
    ['GET', '/example', Example::class . '::list'],
    ['POST', '/example', Example::class . '::register'],
];

$dependencies = [
    ResponseFactoryInterface::class => autowire(Psr17Factory::class),
    StreamFactoryInterface::class => autowire(Psr17Factory::class),
    UploadedFileFactoryInterface::class => autowire(Psr17Factory::class),
    UriFactoryInterface::class => autowire(Psr17Factory::class),
    Router::class => function ($container) {
        return new Router($container, $container->get('routes'));
    },
    SwooleHandlerInterface::class => autowire(DefaultSwooleHandler::class),
    TableRegistryInterface::class => function ($container) {
        $tableRegistry = new TableRegistry();

        $users = new Table(128);
        $users->column('username', Table::TYPE_STRING, 100);
        $users->create();
        $tableRegistry->register('users', $users);

        return $tableRegistry;
    },
    Server::class => create()
        ->constructor(
            get(SwooleHandlerInterface::class),
            get(TableRegistryInterface::class)
        )
];

$container = ContainerFactory::create($config, $dependencies, $routes);

$server = $container->get(Server::class);
$server->run();
