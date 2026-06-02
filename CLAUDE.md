# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install

# Run tests
composer test
# or: vendor/bin/phpunit
# Run a single test file
vendor/bin/phpunit tests/Middleware/JsonBodyParserMiddlewareTest.php

# Code style check / fix
composer cs-check
composer cs-fix

# Static analysis
composer static-analysis
```

## Architecture

This is a PHP library (namespace `Imefisto\SwooleKit`) that bridges Swoole's event-driven HTTP server with PSR-7/PSR-15 standards.

**Request flow:**
1. Swoole fires `onRequest(Request, Response)` to `HttpHandler`
2. `DefaultHttpHandler` converts the Swoole request to PSR-7 via `imefisto/psr-swoole-native`
3. `Router` (wrapping `league/route`) dispatches to a controller class resolved from the DI container
4. The PSR-7 response is merged back into the Swoole response via `ResponseMerger`

**Key abstractions:**

- `SimpleServer` — thin wrapper around `\Swoole\Http\Server` that wires Swoole events to typed handler interfaces. All handlers are optional; pass only the ones you need.
- Handler interfaces (`HttpHandler`, `WebSocketHandler`, `WorkerHandler`, `TaskHandler`, `ServerLifecycleHandler`) — implement these to customize server behavior. `DefaultHttpHandler` is the provided PSR-7-aware HTTP implementation.
- `Router` — wraps `league/route` with a PSR-11 container for controller autowiring. Routes accept per-route and global middlewares.
- `ContainerFactory` — builds a `php-di` container from three arrays: `$config`, `$dependencies` (DI definitions), and `$routes` (`Route[]`). Config and routes are bound under the keys `'config'` and `'routes'`.
- `Route` — value object holding method, path, handler (string class name, callable, or `RequestHandlerInterface`), and optional per-route `MiddlewareInterface` instances.

**Wiring pattern** (see `examples/basic/server.php`):
```php
$container = ContainerFactory::create($config, $dependencies, $routes);
$server = $container->get(SimpleServer::class);
$server->run();
```

Controllers are resolved from the DI container by class name, so PSR interface bindings (e.g. `ResponseFactoryInterface`) must be registered in `$dependencies`.

**PSR-7 implementation:** The library is PSR-7 agnostic. Examples use `nyholm/psr7`, but any compliant factory works — just bind the PSR-17 factory interfaces in the container.

**Test coverage note:** `phpunit.xml` enforces `@covers` annotations and strict coverage. New test classes must include a `@covers` docblock.
