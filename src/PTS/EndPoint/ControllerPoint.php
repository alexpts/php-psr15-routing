<?php
declare(strict_types=1);

namespace PTS\EndPoint;

use Psr\Http\Message\ServerRequestInterface;

class ControllerPoint extends EndPoint
{
    protected function getControllerClass(ServerRequestInterface $request): string
    {
        return $this->controller;
    }
}
