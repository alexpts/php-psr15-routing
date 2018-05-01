<?php
declare(strict_types=1);

namespace PTS\PSR15Routing;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

class CallableAdapter implements RequestHandlerInterface
{
    /** @var callable */
    protected $realHandler;

    public function __construct(callable $handler)
    {
        $this->realHandler = $handler;
    }

    /**
     * @inheritdoc
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        return \call_user_func($this->realHandler, $request);
    }
}
