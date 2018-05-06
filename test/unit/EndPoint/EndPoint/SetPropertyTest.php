<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\TestCase;
use PTS\EndPoint\EndPoint;

/**
 * @covers \PTS\EndPoint\EndPoint::setProperty()
 */
class SetPropertyTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = 'setProperty';

    /**
     * @throws \ReflectionException
     */
    public function testSetProperty(): void
    {
        $endpoint = $this->createMock(EndPoint::class);

        $method = new \ReflectionMethod(self::TEST_CLASS, self::TEST_METHOD);
        $method->setAccessible(true);
        $method->invoke($endpoint, 'controller', 'some');

        $prop = new \ReflectionProperty(self::TEST_CLASS, 'controller');
        $prop->setAccessible(true);
        self::assertSame('some', $prop->getValue($endpoint));
    }
}