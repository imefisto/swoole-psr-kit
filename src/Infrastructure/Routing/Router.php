<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Infrastructure\Routing;

use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Router
{
    private LeagueRouter $router;

    public function __construct(private readonly ContainerInterface $container)
    {
        $this->router = new LeagueRouter();
        $strategy = new ApplicationStrategy();
        $strategy->setContainer($this->container);
        $this->router->setStrategy($strategy);
        $this->loadRoutes($this->container->get('routes'));
    }

    public function loadRoutes(array $routes): void
    {
        foreach ($routes as $route) {
            [$method, $path, $handler, $name] = $route;
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
