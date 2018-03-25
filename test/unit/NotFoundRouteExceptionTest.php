<?php

use PHPUnit\Framework\TestCase;
use PTS\PSR15Routing\NotFoundRouteException;

class NotFoundRouteExceptionTest extends TestCase
{

    public function testCreate(): void
    {
        $exception = new NotFoundRouteException;
        self::assertInstanceOf(NotFoundRouteException::class, $exception);
    }
}