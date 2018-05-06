<?php

namespace EndPointTests\DynamicPoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\DynamicPoint;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\DynamicPoint::getAction()
 */
class GetActionTest extends TestCase
{
    protected const TEST_CLASS = DynamicPoint::class;
    protected const TEST_METHOD = 'getAction';

    /**
     * @param array $matches
     * @param array $params
     * @param null|string $expected
     *
     * @throws \ReflectionException
     *
     * @dataProvider dataProvider
     */
    public function testGetAction(array $matches, array $params, ?string $expected): void
    {
        $route = $this->getMockBuilder(Route::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMatchesParams'])
            ->getMock();
        $route->expects(self::once())->method('getMatchesParams')->willReturn($matches);

        $request = $this->getMockBuilder(ServerRequestInterface::class)
            ->disableOriginalConstructor()
            ->setMethods(['getMethod'])
            ->getMockForAbstractClass();
        $request->method('getMethod')->willReturn('GET');

        /** @var MockObject|EndPoint $endpoint */
        $endpoint = $this->getMockBuilder(self::TEST_CLASS)
            ->setConstructorArgs([$params])
            ->setMethods(['getRoute'])
            ->getMock();
        $endpoint->expects(self::once())->method('getRoute')->with($request)->willReturn($route);

        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $request);

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [
                [],
                [],
                'get',
            ],
            [
                ['_action' => 'get'],
                [],
                'get',
            ],
            [
                ['_action' => 'get'],
                ['action' => 'post'],
                'get',
            ],
            [
                [],
                ['action' => 'post'],
                'post',
            ],
        ];
    }
}