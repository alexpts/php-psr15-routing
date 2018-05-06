<?php
namespace EndPointTests\DynamicPoint;

use BadMethodCallException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\DynamicPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\DynamicPoint::getControllerClass()
 */
class GetControllerClassTest extends TestCase
{
    protected const TEST_CLASS = DynamicPoint::class;
    protected const TEST_METHOD = 'getControllerClass';

    /**
     * @param array $params
     * @param array $matches
     * @param string $expected
     *
     * @throws \ReflectionException
     * @dataProvider dataProvider
     */
    public function testGetControllerClass(array $params, array $matches, ?string $expected): void
    {
        if ($expected === null) {
            $this->expectException(BadMethodCallException::class);
            $this->expectExceptionMessage('Not found controller name for dynamic controller point');
        }

        $controller = $matches['_controller'] ?? '';

        $request = $this->createMock(ServerRequestInterface::class);
        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchesParams'])
            ->getMock();
        $route->expects(self::once())->method('getMatchesParams')->willReturn($matches);

        /** @var MockObject|DynamicPoint $point */
        $point = $this->getMockBuilder(self::TEST_CLASS)
            ->setConstructorArgs([$params])
            ->setMethods(['getRoute', 'normalizeClassFromUrl'])
            ->getMock();
        $point->expects(self::once())->method('getRoute')->with($request)->willReturn($route);
        $point->method('normalizeClassFromUrl')->with($controller)->willReturn($controller);

        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($point, $request);

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [[], [], null],
            [[], ['_controller' => 'some'], 'some'],
            [['prefix' => '\\Namespace\\'], ['_controller' => 'some'], '\\Namespace\\some'],
        ];
    }
}