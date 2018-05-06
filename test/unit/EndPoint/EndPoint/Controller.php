<?php

namespace EndPointTests\EndPoint;

use Psr\Http\Message\ServerRequestInterface;

class Controller
{
    /** @var ServerRequestInterface */
    protected $request;

    public function __construct(ServerRequestInterface $request)
    {
        $this->request = $request;
    }

    public function actionA(): void
    {

    }
}