<?php
declare(strict_types=1);

namespace PTS\EndPoint;

use Psr\Http\Message\ServerRequestInterface;

class DynamicPoint extends EndPoint
{
    protected $prefix = '';

    protected function getControllerClass(ServerRequestInterface $request) : string
    {
        $route = $this->getRoute($request);
        $matches = $route->getMatchesParams();

        if (!array_key_exists('_controller', $matches)) {
            throw new \BadMethodCallException('Not found controller name for dynamic controller point');
        }

        return $this->prefix . $this->normalizeClassFromUrl($matches['_controller']);
    }

    protected function normalizeClassFromUrl(string $class) : string
    {
        return array_reduce(explode('-', $class), function ($prev, $item) {
            return $prev . ucfirst($item);
        });
    }

    protected function getAction(ServerRequestInterface $request): ?string
    {
        return parent::getAction($request) ?? strtolower($request->getMethod());
    }
}
