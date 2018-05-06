<?php
namespace EndPointTests\DynamicPoint;

use BadMethodCallException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\DynamicPoint;
use PTS\PSR15Routing\Route;

/**
 * @covers \PTS\EndPoint\DynamicPoint::normalizeClassFromUrl()
 */
class NormalizeClassFromUrlTest extends TestCase
{
    protected const TEST_CLASS = DynamicPoint::class;
    protected const TEST_METHOD = 'normalizeClassFromUrl';

    /**
     * @param string $class
     * @param string $expected
     *
     * @throws \ReflectionException
     * @dataProvider dataProvider
     */
    public function testNormalizeClassFromUrl(string $class, string $expected): void
    {
        $point = new DynamicPoint;
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($point, $class);

        self::assertSame($expected, $actual);
    }

    public function dataProvider(): array
    {
        return [
            ['', ''],
            ['controller', 'Controller'],
            ['controller-name', 'ControllerName'],
        ];
    }
}