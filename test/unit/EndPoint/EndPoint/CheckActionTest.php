<?php
namespace EndPointTests\EndPoint;

use BadMethodCallException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ServerRequestInterface;
use PTS\EndPoint\EndPoint;

/**
 * @covers \PTS\EndPoint\EndPoint::checkAction()
 */
class CheckActionTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'checkAction';

    /**
     * @param mixed $controller
     * @param string $action
     * @param bool $exception
     *
     * @throws \ReflectionException
     * @dataProvider dataProvider
     */
    public function testCheckAction($controller, string $action, bool $exception): void
    {
        if ($exception) {
            $this->expectException(BadMethodCallException::class);
            $this->expectExceptionMessage('Action not found');
        }

        $endpoint = new EndPoint;
        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $actual = $method->invoke($endpoint, $controller, $action);
        self::assertNull($actual);
    }

    public function dataProvider(): array
    {
        return [
            [EndPoint::class, 'handle', false],
            [new EndPoint, 'handle', false],
            [EndPoint::class, 'handle_bad', true],
            [new EndPoint, 'handle_bad', true],
        ];
    }
}