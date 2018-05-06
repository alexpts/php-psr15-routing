<?php
namespace EndPointTests\EndPoint;

use BadMethodCallException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;

/**
 * @covers \PTS\EndPoint\EndPoint::checkController()
 */
class CheckControllerTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'checkController';

    /**
     * @param string $class
     * @param bool $exception
     *
     * @throws \ReflectionException
     * @dataProvider dataProvider
     */
    public function testCheckController(string $class, bool $exception): void
    {
        if ($exception) {
            $this->expectException(BadMethodCallException::class);
            $this->expectExceptionMessage('Controller not found');
        }

        $endpoint = new EndPoint;
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $class);
        self::assertNull($actual);
    }

    public function dataProvider(): array
    {
        return [
            ['badClass', true],
            [EndPoint::class, false],
        ];
    }
}