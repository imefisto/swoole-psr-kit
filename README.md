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

## Usage

- Run `php examples/basic/server.php` for a basic example. Test it with `curl localhost:8080/example`.

- Run `php examples/table/server.php` to run a version with Swoole table management. Add users with `curl localhost:8080/example -d user=some-user` and `curl localhost:8080/example` to get a list of the registered users.

- Run `php example/middleware/server.php` to see middlewares in action.

## Contributing

Contributions are welcome! Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.
