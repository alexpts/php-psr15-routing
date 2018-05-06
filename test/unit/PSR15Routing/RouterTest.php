<?php
namespace PSR15RoutingTests;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\UriInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PTS\PSR15Routing\NotFoundRouteException;
use PTS\PSR15Routing\Route;
use PTS\PSR15Routing\Router;
use PTS\Tools\DuplicateKeyException;
use ReflectionException;
use ReflectionMethod;

class RouterTest extends TestCase
{
    /** @var Router */
    protected $router;
    /** @var MockObject|RequestHandlerInterface $handler */
    protected $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->router = new Router;
        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
    }

    /**
     * @param string $method
     *
     * @return ReflectionMethod
     *
     * @throws ReflectionException
     */
    protected function getReflectionMethod(string $method): ReflectionMethod
    {
        $reflectionMethod = new \ReflectionMethod(Router::class, $method);
        $reflectionMethod->setAccessible(true);

        return $reflectionMethod;
    }

    /**
     * @throws DuplicateKeyException
     */
    public function testAdd(): void
    {
        $route = new Route('/blob/', $this->handler);

        /** @var MockObject|Router $router */
        $router = $this
            ->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['addItem'])
            ->getMock();
        $router->expects(self::once())->method('addItem')->with('demo', $route, 50)->willReturn($router);

        $router->add('demo',$route);
    }

    public function testRemove(): void
    {
        /** @var MockObject|Router $router */
        $router = $this
            ->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['removeItemWithoutPriority'])
            ->getMock();
        $router->expects(self::once())->method('removeItemWithoutPriority')->with('demo')->willReturn($router);

        $router->remove('demo');
    }

    public function testGetRoutes(): void
    {
        $expected = [3 => 3];

        /** @var MockObject|Router $router */
        $router = $this
            ->getMockBuilder(Router::class)
            ->disableOriginalConstructor()
            ->setMethods(['getFlatItems'])
            ->getMock();
        $router->expects(self::once())->method('getFlatItems')->with(true)->willReturn($expected);

        self::assertSame($expected, $router->getRoutes(true));
    }

    /**
     * @param string $path
     * @param string $expected
     * @param array $restriction
     *
     * @dataProvider dataProviderRegExp
     */
    public function testRegexp(string $path, string $expected, array $restriction = []): void
    {
        $route = new Route($path, $this->handler);
        $router = new Router;

        $route->setRestrictions($restriction);

        $regexp = $router->makeRegExp($route);
        self::assertEquals($expected, $regexp);
    }

    public function dataProviderRegExp(): array
    {
        return [
            'simple' => ['/controller/action/', '/controller/action/'],
            'placeholder' => ['/controller/{action}/', '/controller/(?<action>[^\/]+)/'],
            'placeholder + restriction' => ['/controller/{action}/', '/controller/(?<action>\w+)/', ['action' => '\w+']],
        ];
    }

    /**
     * @param bool $expected
     * @param string $method
     * @param array $allowMethods
     *
     * @throws ReflectionException
     *
     * @dataProvider dataProviderIsAllowMethod
     */
    public function testIsAllowMethod(bool $expected, string $method, array $allowMethods = []): void
    {
        $route = new Route('/', $this->handler);
        $route->setMethods($allowMethods);

        $isAllowMethod = $this->getReflectionMethod('isAllowMethod');
        $actual = $isAllowMethod->invoke(new Router, $route, $method);

        self::assertSame($expected, $actual);
    }

    public function dataProviderIsAllowMethod(): array
    {
        return [
            'any' => [true, 'GET'],
            'any #2' => [true, 'POST'],
            'any #3' => [true, 'PATCH'],
            'only GET' => [true, 'GET', ['GET']],
            'only POST - negative' => [false, 'GET', ['POST']],
            [false, 'GET', ['PATCH', 'POST']],
            [true, 'POST', ['PATCH', 'POST']],
        ];
    }

    /**
     * @throws ReflectionException
     */
    public function testMatchRouteNotFound(): void
    {
        $matchRoute = $this->getReflectionMethod('matchRoute');
        $route = new Route('/', $this->handler);

        $actual = $matchRoute->invoke(new Router, $route, '/blog/', 'GET');
        self::assertNull($actual);

        $route->setMethods(['POST']);
        $actual2 = $matchRoute->invoke(new Router, $route, '/blog/', 'GET');
        self::assertNull($actual2);
    }

    /**
     * @throws ReflectionException
     */
    public function testMatchRoute(): void
    {
        $matchRoute = $this->getReflectionMethod('matchRoute');
        $route = new Route('/web/blog/{id}/', $this->handler);

        /** @var Route $actual */
        $actual = $matchRoute->invoke(new Router, $route, '/web/blog/3/', 'GET');
        self::assertInstanceOf(Route::class, $actual);
        self::assertSame(['id' => '3'], $actual->getMatchesParams());
    }

    /**
     * @throws ReflectionException
     */
    public function testMatchRouteOnGreedy(): void
    {
        $matchRoute = $this->getReflectionMethod('matchRoute');
        $route = new Route('/{controller}/({action}/)?', $this->handler);

        /** @var Route $actual */
        $actual = $matchRoute->invoke(new Router, $route, '/post/delete/', 'GET');
        self::assertInstanceOf(Route::class, $actual);
        self::assertSame(['controller' => 'post', 'action' => 'delete'], $actual->getMatchesParams());
    }

    /**
     * @throws DuplicateKeyException
     * @throws NotFoundRouteException
     */
    public function testMatch(): void
    {
        $path = '/web/posts/43/';

        $router = new Router;
        $router->add('demo1', new Route('/{controller}/({action}/)?', $this->handler));
        $router->add('posts', new Route('/web/posts/{id}/', $this->handler));

        $uri = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPath'])
            ->getMockForAbstractClass();
        $uri->expects(self::once())->method('getPath')->willReturn($path);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['getUri', 'getMethod'])
            ->getMockForAbstractClass();
        $request->method('getMethod')->willReturn('GET');
        $request->expects(self::once())->method('getUri')->willReturn($uri);

        $actual = $router->match($request);
        self::assertInstanceOf(Route::class, $actual);
        self::assertSame(['id' => '43'], $actual->getMatchesParams());
    }

    /**
     * @throws DuplicateKeyException
     * @throws NotFoundRouteException
     */
    public function testMatchNotFound(): void
    {
        $path = '/unknown-path';

        $router = new Router;
        $router->add('demo1', new Route('/{controller}/({action}/)?', $this->handler));
        $router->add('posts', new Route('/web/posts/{id}/', $this->handler));

        $uri = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchesParams'])
            ->getMockForAbstractClass();
        $uri->expects(self::once())->method('getPath')->willReturn($path);

        /** @var MockObject|ServerRequestInterface $request */
        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->setMethods(['getUri', 'getMethod'])
            ->getMockForAbstractClass();
        $request->method('getMethod')->willReturn('GET');
        $request->expects(self::once())->method('getUri')->willReturn($uri);

        $this->expectException(NotFoundRouteException::class);
        $this->expectExceptionMessage('Route not found');

        $router->match($request);
    }
}