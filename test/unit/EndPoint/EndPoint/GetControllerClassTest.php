<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;

/**
 * @covers \PTS\EndPoint\EndPoint::getControllerClass()
 */
class GetControllerClassTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'getControllerClass';

    /**
     * @param array $params
     * @param string $expected
     *
     * @throws \ReflectionException
     * @dataProvider dataProvider
     */
    public function testGetControllerClass(array $params, string $expected): void
    {
        $request = $this->createMock(ServerRequestInterface::class);

        $endpoint = new EndPoint($params);
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $request);

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            [[], ''],
            [['controller' => '\\Namespace\\Some'], '\\Namespace\\Some'],
        ];
    }
}