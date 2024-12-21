# Swoole PSR Kit

A PSR-compliant toolkit for building high-performance HTTP and WebSocket servers using Swoole with dependency management.

## Features

- PSR-7 HTTP message interfaces
- PSR-15 middleware support
- PSR-11 container integration
- Named routes support via League Router
- WebSocket support
- Clean architecture structure

## Installation

```bash
composer require imefisto/swoole-psr-kit
```

## Basic Usage

```php
use Imefisto\SwooleKit\Container\ContainerFactory;
use Imefisto\SwooleKit\Server\Server;
use Imefisto\SwooleKit\Routing\Router;

// Create container
$container = ContainerFactory::create($config, [], $routes);

// Setup router
$router = new Router();
$router->loadRoutes([
        ['GET', '/', 'App\Controller\HomeController::index', 'home'],
]);

// Create and run server
$server = new Server($container, $router);
$server->run();
```

## Documentation

For detailed documentation, please see the [/docs](/docs) directory.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
