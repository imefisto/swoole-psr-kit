<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Infrastructure\DependencyInjection;

use DI\ContainerBuilder;
use Psr\Container\ContainerInterface;

class ContainerFactory
{
    public static function create(
        array $config,
        array $dependencies,
        array $routes
    ): ContainerInterface {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->addDefinitions(
            [
                'config' => $config,
                'routes' => $routes
            ]
        );
        $containerBuilder->addDefinitions($dependencies);

        return $containerBuilder->build();
    }
}
