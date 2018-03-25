<?php
declare(strict_types=1);

namespace PTS\PSR15Routing;

use Psr\Http\Server\RequestHandlerInterface;

class Route
{
    /** @var string */
    protected $path;
    /** @var RequestHandlerInterface */
    protected $handler;
    /** @var array */
    protected $matches = [];
    /** @var array */
    protected $methods = [];
    /** @var array */
    protected $restrictions = [];

    public function __construct(string $path, RequestHandlerInterface $handler)
    {
        $this->path = $path;
        $this->handler = $handler;
    }

    public function setMatches(array $values = []): self
    {
        $this->matches = $values;
        return $this;
    }

    public function getMatchesParams(): array
    {
        return $this->matches;
    }

    public function getMethods(): array
    {
        return $this->methods;
    }

    public function setMethods(array $methods): self
    {
        $this->methods = $methods;
        return $this;
    }

    public function setRestrictions(array $restrictions): self
    {
        $this->restrictions = $restrictions;
        return $this;
    }

    public function getRestrictions(): array
    {
        return $this->restrictions;
    }

    public function getHandler(): RequestHandlerInterface
    {
        return $this->handler;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}