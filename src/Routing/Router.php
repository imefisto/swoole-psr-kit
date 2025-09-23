<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Routing;

use League\Route\Router as LeagueRouter;
use League\Route\Strategy\ApplicationStrategy;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;

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
        foreach ($routes as $route) {
            if (!$route instanceof Route) {
                throw new \InvalidArgumentException('Expected Route object');
            }

            $leagueRoute = $this->router->map(
                $route->method,
                $route->path,
                $route->handler
            );

            foreach ($route->getMiddlewares() as $middleware) {
                $leagueRoute->middleware($middleware);
            }
        }
    }

    public function addMiddleware(MiddlewareInterface $middleware): void
    {
        $this->router->middleware($middleware);
    }

    public function dispatch(ServerRequestInterface $request): ResponseInterface
    {
        return $this->router->dispatch($request);
    }
}
