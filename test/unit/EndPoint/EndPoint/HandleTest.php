<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\EndPoint::handle()
 */
class HandleTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'handle';

    /**
     * @throws \ReflectionException
     */
    public function testHandle(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);
        $route = $this->createMock(Route::class);
        $response = $this->createMock(ResponseInterface::class);

        /** @var MockObject|EndPoint $endpoint */
        $endpoint = $this->getMockBuilder(EndPoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['getRoute', 'getPoint', 'getParams'])
            ->getMock();
        $endpoint->expects(self::once())->method('getRoute')->with($request)->willReturn($route);
        $endpoint->expects(self::once())->method('getParams')->with($route)->willReturn([]);
        $endpoint->expects(self::once())->method('getPoint')->with($request)->willReturn(function () use ($response) {
            return $response;
        });

        $actual = $endpoint->handle($request);
        self::assertInstanceOf(ResponseInterface::class, $actual);
    }
}