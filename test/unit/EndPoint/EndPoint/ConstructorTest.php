<?php
namespace EndPointTests\EndPoint;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use PTS\EndPoint\EndPoint;

/**
 * @covers \PTS\EndPoint\EndPoint::__construct()
 */
class ConstructorTest extends TestCase
{
    protected const TEST_CLASS = EndPoint::class;
    protected const TEST_METHOD = '__construct';

    public function testConstructor(): void
    {
        /** @var MockObject|EndPoint $endpoint */
        $endpoint = $this->getMockBuilder(EndPoint::class)
            ->disableOriginalConstructor()
            ->setMethods(['setProperty'])
            ->getMock();
        $endpoint->expects(self::exactly(1))->method('setProperty')->with('controller', 'some2');

        $endpoint->__construct(['controller' => 'some2']);
    }
}