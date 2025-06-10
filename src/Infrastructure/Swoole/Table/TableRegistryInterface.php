<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Infrastructure\Swoole\Table;

use Swoole\Table;

interface TableRegistryInterface
{
    public function register(string $name, Table $table): void;
    public function get(string $name): Table;
    public function has(string $name): bool;
}
