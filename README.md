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
use Imefisto\SwooleKit\Infrastructure\DependencyInjection\ContainerFactory;
use Imefisto\SwooleKit\Infrastructure\Routing\Router;
use Imefisto\SwooleKit\Infrastructure\Swoole\Server;

require __DIR__ . '/vendor/autoload.php';

$config = include __DIR__ . '/src/config/config.php';
$dependencies = include __DIR__ . '/src/config/dependencies.php';
$routes = include __DIR__ . '/src/config/routes.php';

$container = ContainerFactory::create($config, $dependencies, $routes);

$server = $container->get(Server::class);
$server->run();
```

## Documentation

For detailed documentation, please see the [/docs](/docs) directory.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
