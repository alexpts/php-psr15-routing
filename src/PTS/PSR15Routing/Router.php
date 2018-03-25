<?php
declare(strict_types=1);

namespace PTS\PSR15Routing;

use Psr\Http\Message\ServerRequestInterface;
use PTS\Tools\Collection;
use PTS\Tools\DuplicateKeyException;

class Router extends Collection
{
    /**
     * @param string $name
     * @param Route $route
     * @param int $priority
     *
     * @return Router
     * @throws DuplicateKeyException
     */
    public function add(string $name, Route $route, int $priority = 50): self
    {
        return $this->addItem($name, $route, $priority);
    }

    public function remove(string $name): self
    {
        return $this->removeItemWithoutPriority($name);
    }

    public function getRoutes(bool $sortByPriority = true): array
    {
        return $this->getFlatItems($sortByPriority);
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return Route
     *
     * @throws NotFoundRouteException
     */
    public function match(ServerRequestInterface $request): Route
    {
        $uri = $request->getUri()->getPath();

        foreach ($this->getRoutes() as $route) {
            $activeRoute = $this->matchRoute($route, $uri, $request->getMethod());
            if ($activeRoute !== null) {
                return $activeRoute;
            }
        }

        throw new NotFoundRouteException('Route not found');
    }

    protected function isAllowMethod(Route $route, string $method): bool
    {
        $allows = $route->getMethods();
        return \count($allows) ? \in_array($method, $allows, true) : true;
    }

    protected function matchRoute(Route $route, string $pathUrl, string $method): ?Route
    {
        if (!$this->isAllowMethod($route, $method)) {
            return null;
        }

        $regexp = $this->makeRegExp($route);

        if (preg_match('~^'.$regexp.'$~Uiu', $pathUrl, $values)) {
            $filterValues = array_filter(array_keys($values), '\is_string');
            $matches = array_intersect_key($values, array_flip($filterValues));
            return $route->setMatches($matches);
        }

        return null;
    }

    public function makeRegExp(Route $route): string
    {
        $regexp = $route->getPath();
        $restrictions = $route->getRestrictions();
        $placeholders = [];

        if (preg_match_all('~{(.*)}~Uu', $regexp, $placeholders)) {
            foreach ($placeholders[0] as $index => $match) {
                $name = $placeholders[1][$index];
                $replace = array_key_exists($name, $restrictions) ? $restrictions[$name] : '[^\/]+';
                $replace = '(?<'.$name.'>'.$replace.')';
                $regexp = str_replace($match, $replace, $regexp);
            }
        }

        return $regexp;
    }
}