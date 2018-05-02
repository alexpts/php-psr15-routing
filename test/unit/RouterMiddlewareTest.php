<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PTS\PSR15Routing\NotFoundRouteException;
use PTS\PSR15Routing\Route;
use PTS\PSR15Routing\Router;
use PTS\PSR15Routing\RouterMiddleware;

class RouterMiddlewareTest extends TestCase
{
    /**
     * @throws ReflectionException
     */
    public function testCreate(): void
    {
        $router = new Router;
        $middleware = new RouterMiddleware($router);

        $propertyRouter = new \ReflectionProperty(RouterMiddleware::class, 'router');
        $propertyRouter->setAccessible(true);
        $propValue = $propertyRouter->getValue($middleware);

        self::assertInstanceOf(Router::class, $propValue);
    }

    /**
     * @throws NotFoundRouteException
     * @throws ReflectionException
     */
    public function testProcess(): void
    {
        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->setMethods(['getHandler'])
            ->getMock();

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['withAttribute'])
            ->getMockForAbstractClass();
        $request->expects(self::once())->method('withAttribute')->with('route', $route)->willReturn($request);

        /** @var MockObject|RequestHandlerInterface $handler */
        $handler = $this->getMockBuilder(RequestHandlerInterface::class)
            ->setMethods(['handle'])
            ->getMock();
        $handler->expects(self::once())->method('handle')->with($request)
            ->willReturn($this->createMock(ResponseInterface::class));

        $route->expects(self::once())->method('getHandler')->willReturn($handler);

        /** @var MockObject|Router $router */
        $router = $this->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['match'])
            ->getMock();
        $router->expects(self::once())->method('match')->with($request)->willReturn($route);

        $middleware = new RouterMiddleware($router);
        $middleware->process($request, $handler);
    }
}