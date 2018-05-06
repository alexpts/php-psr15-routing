<?php
namespace PSR15RoutingTests;

use Closure;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use PTS\PSR15Routing\CallableAdapter;
use ReflectionException;
use ReflectionProperty;

class CallableAdapterTest extends TestCase
{
    /**
     * @covers \PTS\PSR15Routing\CallableAdapter::__construct
     * @throws \ReflectionException
     */
    public function testConstuctor(): void
    {
        $adapter = new CallableAdapter(function () {
            return true;
        });

        $prop = new ReflectionProperty(CallableAdapter::class, 'realHandler');
        $prop->setAccessible(true);
        $actual = $prop->getValue($adapter);
        self::assertInstanceOf(Closure::class, $actual);
    }

    /**
     * @covers \PTS\PSR15Routing\CallableAdapter::handle()
     * @throws \ReflectionException
     */
    public function testHandle(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $request = $this->createMock(ServerRequestInterface::class);

        $adapter = new CallableAdapter(function (ServerRequestInterface $request) use ($response) {
            return $response;
        });

        $actual = $adapter->handle($request);
        self::assertInstanceOf(ResponseInterface::class, $actual);
    }
}