<?php

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Server\RequestHandlerInterface;
use PTS\PSR15Routing\Route;

class RouteTest extends TestCase
{
    /** @var Route */
    protected $route;
    /** @var MockObject|RequestHandlerInterface $handler */
    protected $handler;

    protected function setUp(): void
    {
        parent::setUp();

        $this->handler = $this->getMockBuilder(RequestHandlerInterface::class)->getMock();
        $this->route = new Route('/', $this->handler);
    }

    public function testCreate(): void
    {
        $route = new Route('/', $this->handler);
        self::assertInstanceOf(Route::class, $route);
    }

    public function testPath(): void
    {
       self::assertEquals('/', $this->route->getPath());
    }

    public function testMethods(): void
    {
        $data = ['get', 'post'];
        $this->route->setMethods($data);
        self::assertEquals($data, $this->route->getMethods());
    }

    public function testMatches(): void
    {
        $data = ['controller' => 'demo', 'action' => 'index'];
        $this->route->setMatches($data);
        self::assertEquals($data, $this->route->getMatchesParams());
    }

    public function testRestrictions(): void
    {
        $data = ['controller' => '\w+', 'id' => '\d+'];
        $this->route->setRestrictions($data);
        self::assertEquals($data, $this->route->getRestrictions());
    }

    public function testGetHandler(): void
    {
        $actual = $this->route->getHandler();
        self::assertSame($this->handler, $actual);
        self::assertInstanceOf(RequestHandlerInterface::class, $actual);
    }
}