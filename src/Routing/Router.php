<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Routing;

use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private LeagueRouter $router;

    public function __construct(ContainerInterface $container, array $routes)
    {
        $this->router = new LeagueRouter();
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($container);
        $this->router->setStrategy($strategy);
        $this->loadRoutes($routes);
    }

    public function loadRoutes(array $routes): void
    {
        foreach ($routes as $routeConfig) {
            [$method, $path, $handler] = $routeConfig;
            $name = $routeConfig[3] ?? null;

            $route = $this->router->map($method, $path, $handler);

            if ($name) {
                $route->setName($name);
            }
        }
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }
}
