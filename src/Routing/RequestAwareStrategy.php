<?php

declare(strict_types=1);

namespace Imefisto\SwooleKit\Routing;

use League\Route\Strategy\ApplicationStrategy;
use League\Route\Route;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RequestAwareStrategy extends ApplicationStrategy
{
    public function invokeRouteCallable(
        Route $route,
        ServerRequestInterface $request
    ): ResponseInterface {
        $request = $request
            ->withAttribute('routeName', $route->getName());

        return parent::invokeRouteCallable($route, $request);
    }
}
