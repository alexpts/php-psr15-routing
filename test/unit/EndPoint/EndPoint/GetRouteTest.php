<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\EndPoint::getRoute()
 */
class GetRouteTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'getRoute';

    /**
     * @throws \ReflectionException
     */
    public function testGetRoute(): void
    {
        $route = $this->createMock(Route::class);

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getAttribute'])
            ->getMockForAbstractClass();
        $request->expects(self::once())->method('getAttribute')->with('route')->willReturn($route);

        $endpoint = new EndPoint;
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $request);

        self::assertInstanceOf(Route::class, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [
                [],
                [],
            ],
            [
                ['id' => 1],
                ['id' => 1],
            ],
            [
                ['id' => 2, '_controller' => 'some'],
                ['id' => 2],
            ],
            [
                ['id' => 3, 'name' => 'some'],
                ['id' => 3, 'name' => 'some'],
            ],
        ];
    }
}