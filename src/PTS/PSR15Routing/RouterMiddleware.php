<?php
declare(strict_types=1);

namespace PTS\PSR15Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

class RouterMiddleware implements MiddlewareInterface
{
    protected $attributeName = 'route';

    /** @var Router */
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     *
     * @return ResponseInterface
     *
     * @throws NotFoundRouteException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $route = $this->router->match($request);
        $request = $request->withAttribute($this->attributeName, $route);
        return $route->getHandler()->handle($request);
    }
}