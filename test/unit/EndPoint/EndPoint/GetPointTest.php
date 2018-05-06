<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;
use PTS\PSR15Routing\Route;

require_once __DIR__ . '/Controller.php';

/**
 * @covers \PTS\EndPoint\EndPoint::getPoint()
 */
class GetPointTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'getPoint';

    /**
     * @throws \ReflectionException
     */
    public function testGetPoint(): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        /** @var MockObject|EndPoint $endpoint */
        $endpoint = $this->getMockBuilder(EndPoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['getControllerClass', 'getAction', 'checkAction', 'checkController'])
            ->getMock();
        $endpoint->expects(self::once())->method('getControllerClass')->with($request)->willReturn(Controller::class);
        $endpoint->expects(self::once())->method('getAction')->with($request)->willReturn('actionA');

        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $request);

        self::assertCount(2, $actual);
        self::assertInternalType('callable', $actual);
        self::assertInstanceOf(Controller::class, $actual[0]);
        self::assertSame('actionA', $actual[1]);
    }
}