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

Install the package via Composer and require the PSR-7 implementation of your choice:

```bash
composer require imefisto/swoole-psr-kit
composer require http-interop/http-factory-guzzle

// or

composer require nyholm/psr7
```

## Basic Usage

Lets setup a basic example using our provided Example controller.

Create a `config.php` file with your server configuration:

```php
return [
    // some configuration
]
```

Create a `dependencies.php` file with your container definitions. Example using Guzzle PSR package:

```php
use Http\Factory\Guzzle\ResponseFactory;
use Http\Factory\Guzzle\StreamFactory;
use Http\Factory\Guzzle\UploadedFileFactory;
use Http\Factory\Guzzle\UriFactory;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;

return [
    ResponseFactoryInterface::class => \DI\get(ResponseFactory::class),
    StreamFactoryInterface::class => \DI\get(StreamFactory::class),
    UploadedFileFactoryInterface::class => \DI\get(UploadedFileFactory::class),
    UriFactoryInterface::class => \DI\get(UriFactory::class),
];
```

Create a `routes.php` file with your routes:

```php
use Imefisto\SwooleKit\Presentation\Controller\Example;

return [
    ['GET', '/example', Example::class, 'getExample'],
];
```

Create a `server.php` file with your server implementation:

```php
use Imefisto\SwooleKit\Infrastructure\DependencyInjection\ContainerFactory;
use Imefisto\SwooleKit\Infrastructure\Routing\Router;
use Imefisto\SwooleKit\Infrastructure\Swoole\Server;

require __DIR__ . '/vendor/autoload.php';

// assumes you've created the config files within src/config
$config = require_once __DIR__ . '/src/config/config.php';
$dependencies = require_once __DIR__ . '/src/config/dependencies.php';
$routes = require_once __DIR__ . '/src/config/routes.php';

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
