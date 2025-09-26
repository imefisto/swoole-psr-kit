# SwooleKit

[![Latest Version on Packagist](https://img.shields.io/packagist/v/imefisto/swoole-psr-kit.svg?style=flat-square)](https://packagist.org/packages/imefisto/swoole-psr-kit)
[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square)](LICENSE)

A PHP library providing clean interfaces and handlers for Swoole server implementation, with support for HTTP, WebSocket, and Worker management.

> ⚠️ **Alpha Release**: This project is currently in alpha stage (v2.x-alpha). The API may change significantly between versions. Version 2.x contains breaking changes from 1.x.

## Version Information

- **Current**: `2.0.0-alpha.1` (Alpha - Breaking changes from 1.x)
- **Previous**: `1.1.0` (Legacy - Not recommended for new projects)

### Migration from 1.x

If you're upgrading from version 1.x, please note there are breaking changes in 2.x. See [CHANGELOG.md](CHANGELOG.md) for detailed migration information.

## Requirements

- PHP 8.3+
- Swoole 5.1+

## Installation

For new projects (recommended):
```bash
composer require imefisto/swoole-psr-kit:^2.0@alpha
```

For existing 1.x projects:
```bash
composer require imefisto/swoole-psr-kit:^1.1
```

## Features

- HTTP request handling with PSR-7 compliance
- WebSocket server support with clean event handlers
- Worker management interface
- Middleware support
- Extensible handler system

## Basic Usage

See the folder `examples/`.

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.
