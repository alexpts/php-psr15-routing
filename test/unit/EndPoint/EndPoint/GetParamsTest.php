<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\TestCase;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\EndPoint::getParams()
 */
class GetParamsTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'getParams';

    /**
     * @param array $matcher
     * @param array $expected
     *
     * @throws \ReflectionException
     *
     * @dataProvider dataProvider
     */
    public function testGetParams(array $matcher, array $expected): void
    {
        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchesParams'])
            ->getMock();
        $route->expects(self::once())->method('getMatchesParams')->willReturn($matcher);

        $endpoint = new EndPoint;
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $route);

        self::assertSame($expected, $actual);
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