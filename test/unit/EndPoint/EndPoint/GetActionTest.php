<?php

namespace EndPointTests\EndPoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\EndPoint::getAction()
 */
class GetActionTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
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

        $request = $this->createMock(ServerRequestInterface::class);

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
                null,
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