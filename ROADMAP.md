# Roadmap

## Short term (2.x)
- [ ] Make port parametrizable
- [ ] PHP 8.4 compatibility (see plan below)
- [ ] More examples and documentation

---

## PHP 8.4 compatibility via Rector

PHP 8.4 deprecates implicit nullable parameters (`Type $param = null` → `?Type $param = null`).
The affected file is `src/Swoole/SimpleServer.php` (the `$server` parameter).

Downstream consumers also trigger deprecations from `php-di/php-di`, `opis/closure`, and `psr/http-factory`
— those are upstream problems to track, not fix here.

### Plan

1. **Add Rector** as a dev dependency:
   ```
   composer require --dev rector/rector
   ```

2. **Configure `rector.php`** at project root with the PHP 8.4 set:
   ```php
   use Rector\Config\RectorConfig;
   use Rector\Set\ValueObject\SetList;

   return RectorConfig::configure()
       ->withPaths([__DIR__ . '/src'])
       ->withSets([SetList::PHP_84]);
   ```
   The relevant rule is `Rector\TypeDeclaration\Rector\FunctionLike\AddParamTypeBasedOnPropFetchRector`
   (or more precisely the implicit-nullable rule: `Rector\Php84\Rector\Param\ExplicitNullableParamTypeRector`).

3. **Run and review**:
   ```
   vendor/bin/rector process --dry-run
   vendor/bin/rector process
   ```

4. **Update `composer.json`** PHP constraint:
   ```
   "php": "^8.1 || ^8.4"
   ```
   (or just `"^8.1"` if the rest of the codebase already supports 8.4 without issues)

5. **Tag `2.0.1`** (patch — no API change, only type annotation fix).

### What changes

Only `SimpleServer.php` line 23:
```php
// before
\Swoole\Server $server = null
// after
?\Swoole\Server $server = null
```

---

## Decoupling clients from Swoole

### Problem

Client code that implements any handler interface currently has a hard dependency on Swoole types in method signatures:

```php
// Every WebSocket handler a client writes must import these:
use Swoole\WebSocket\Server;
use Swoole\WebSocket\Frame;
use Swoole\Http\Request;

class MyWebSocketHandler implements WebSocketHandler
{
    public function onMessage(Server $server, Frame $frame): void { ... }
}
```

This means:
- Unit tests require Swoole installed in the test environment, or need mocks of Swoole internals.
- CI environments without the Swoole extension cannot run any tests that touch handler code.
- Controllers are already clean (PSR-7), but WebSocket, worker, task, and lifecycle handlers are fully coupled to Swoole.

The HTTP side is partially solved: `DefaultHttpHandler` converts Swoole → PSR-7 internally so controllers stay clean. The goal is to extend that pattern to all handler surfaces, making `SimpleServer` the single seam where Swoole is imported.

---

### Phase 1 — PSR-15 HTTP handler interface

Introduce a `PsrHttpHandler` interface alongside the existing `HttpHandler`:

```php
interface PsrHttpHandler
{
    public function handle(ServerRequestInterface $request): ResponseInterface;
}
```

`DefaultHttpHandler` is updated to accept a `PsrHttpHandler` and delegate to it, handling the Swoole ↔ PSR-7 conversion internally. Clients who only need HTTP can implement `PsrHttpHandler` without touching a Swoole type.

`HttpHandler` stays unchanged. This is purely additive — no breaking changes.

---

### Phase 2 — Library-owned event types

Replace Swoole types in all remaining handler interfaces with plain PHP value objects owned by this library:

```php
// WebSocket
interface WebSocketHandler
{
    public function onOpen(WebSocketConnection $connection, ServerRequestInterface $request): void;
    public function onMessage(WebSocketConnection $connection, IncomingFrame $frame): void;
    public function onClose(WebSocketConnection $connection): void;
}

// Worker
interface WorkerHandler
{
    public function onWorkerStart(WorkerContext $context): void;
    public function onWorkerError(WorkerContext $context, int $pid, int $exitCode, int $signal): void;
    public function onWorkerExit(WorkerContext $context): void;
}
```

The `SimpleServer` adapter layer translates raw Swoole events into these objects before dispatching. Lifecycle and task handlers follow the same pattern.

This is the largest breaking change and requires a major version bump. Old interfaces can be kept under a `Legacy\` namespace with a deprecation notice through a transition period.

---

### Phase 3 — Narrow server-interaction interfaces

Handlers often need to reply (e.g., push a WebSocket message). Currently they receive the `Swoole\WebSocket\Server` object for that. Replace it with a focused interface:

```php
interface WebSocketPusher
{
    public function push(int $fd, string $data): void;
    public function close(int $fd): void;
}
```

The concrete Swoole implementation wraps `\Swoole\WebSocket\Server`. A test double can implement the same interface in-memory. Handlers receive this as a constructor dependency via the DI container rather than as a callback argument.

Similarly for tasks:

```php
interface TaskDispatcher
{
    public function dispatch(mixed $data): int;
    public function finish(mixed $result): void;
}
```

After this phase, no handler interface requires a Swoole type anywhere.

---

### Phase 4 — Test utilities

Ship test helpers under a `Testing\` namespace (or a companion `imefisto/swoole-psr-kit-testing` package):

- **`InMemoryWebSocketPusher`** — implements `WebSocketPusher`, records pushed frames for assertion.
- **`FakeTaskDispatcher`** — implements `TaskDispatcher`, captures dispatched tasks.
- **`TestHttpKernel`** — wraps the router and PSR handler to dispatch a `ServerRequestInterface` and return a `ResponseInterface` without starting a server. Analogous to Symfony's `HttpKernelBrowser`.
- **`RequestBuilder`** — fluent builder for `ServerRequestInterface` to reduce test setup boilerplate.

These are most valuable once Phases 1–3 are stable.

---

### What doesn't change

- `ContainerFactory` and the DI wiring pattern — already Swoole-agnostic.
- `Router` and `Route` — already PSR-7/PSR-15.
- `JsonBodyParserMiddleware` and middleware support generally — already clean.
- The `SimpleServer` class remains the Swoole-specific adapter layer; it just becomes the *only* place in the library that imports Swoole types.
