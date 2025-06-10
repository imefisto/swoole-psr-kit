<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Infrastructure\Swoole\Table;

use Swoole\Table;

class TableRegistry implements TableRegistryInterface
{
    private array $tables = [];

    public function register(string $name, Table $table): void
    {
        if (isset($this->tables[$name])) {
            throw new \RuntimeException("Table {$name} already registered");
        }

        $this->tables[$name] = $table;
    }

    public function get(string $name): Table
    {
        if (!isset($this->tables[$name])) {
            throw new \RuntimeException("Table {$name} not found");
        }

        return $this->tables[$name];
    }

    public function has(string $name): bool
    {
        return isset($this->tables[$name]);
    }
}
