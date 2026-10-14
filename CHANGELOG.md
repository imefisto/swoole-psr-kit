# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [2.0.0-alpha.3] - 2026-10-14

### Added
- `Testing\TestHttpKernel` dispatches PSR-7 requests through the router without starting a server.
- `SimpleServer` accepts `host` and `port` constructor arguments (default `0.0.0.0:8080`).

### Fixed
- PHP 8.4 deprecation: `SimpleServer`'s `$server` parameter is now explicitly nullable.
- `HttpException` subclasses (e.g. `UnauthorizedException`) now respond with their status code instead of 500.
- Headers from League route exceptions (e.g. `Allow` on 405) are now sent.
- `SimpleServer` creates a `Swoole\WebSocket\Server` by default when a `WebSocketHandler` is given; WebSocket events were silently ignored on the plain HTTP server.
- `WorkerHandler::onWorkerError()` and `onWorkerExit()` are now registered.
- `composer cs-check` failed due to an invalid sniff reference in `phpcs.xml.dist`.

## [2.0.0-alpha.2] - 2025-09-26

### Changed
- README update.

## [2.0.0-alpha.1] - 2025-09-23

### ⚠️ Breaking Changes
- The Server was renamed to SimpleServer. The constructor will allow composition.
- Different interface were added for implement the callbacks for different events.
- The folder structure was simplified.

## [1.1.0]

### Changed
- Make name for routes optional
- Update dependencies

## [1.0.0]

### Removed
- Removed configuration files - users should provide their own config files
- Removed http-interop/http-factory-guzzle dependency - users should choose their preferred PSR HTTP factories

## [0.1.0]

### Added
- Initial release with core components
- PSR-7 and PSR-15 support
- League Router integration
- Container Factory
