<?php
declare(strict_types=1);

namespace PTS\EndPoint;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use PTS\PSR15Routing\Route;

class EndPoint implements RequestHandlerInterface
{
	/** @var string|null */
	protected $controller;
	/** @var string|null */
	protected $action;

    public function __construct(array $params = [])
    {
        foreach ($params as $name => $param) {
            if (property_exists($this, $name)) {
                $this->setProperty($name, $param);
            }
        }
    }

    /**
     * @param ServerRequestInterface $request
     *
     * @return ResponseInterface
     * @throws \BadMethodCallException
     */
    public function handle(ServerRequestInterface $request): ResponseInterface
	{
    	$route = $this->getRoute($request);
        $endPoint = $this->getPoint($request);

        $params = $this->getParams($route);
        return $endPoint(...$params);
    }

    protected function setProperty(string $name, $value): void
    {
        $this->{$name} = $value;
    }

	/**
	 * Get params from router and remove all protected params (_controller/_action/_other)
	 *
	 * @param Route $route
	 * @return array
	 */
    protected function getParams(Route $route): array
	{
        return array_filter($route->getMatchesParams(), function ($name) {
            return $name[0] !== '_';
        }, ARRAY_FILTER_USE_KEY);
	}

    /**
     * @param ServerRequestInterface $request
     *
     * @return callable
     * @throws \BadMethodCallException
     */
	protected function getPoint(ServerRequestInterface $request) : callable
    {
        $controller = $this->getControllerClass($request);
        $this->checkController($controller);

        $controller = new $controller($request);

        $action = $this->getAction($request) ?? 'index';
        $this->checkAction($controller, $action);

        return [$controller, $action];
    }

    protected function getControllerClass(ServerRequestInterface $request): string
    {
        return $this->controller ?? '';
    }

	/**
	 * @param string $controller
	 * @throws \BadMethodCallException
	 */
	protected function checkController(string $controller) : void
    {
        if (!class_exists($controller)) {
            throw new \BadMethodCallException('Controller not found');
        }
    }

	/**
	 * @param \object $controller
	 * @param string $action
	 * @throws \BadMethodCallException
	 */
	protected function checkAction($controller, string $action) : void
    {
        if (!method_exists($controller, $action)) {
            throw new \BadMethodCallException('Action not found');
        }
    }

	protected function getAction(ServerRequestInterface $request): ?string
	{
        $route = $this->getRoute($request);
        $matches = $route->getMatchesParams();
		return $matches['_action'] ?? $this->action ?? null;
	}

	protected function getRoute(ServerRequestInterface $request): Route
    {
        return $request->getAttribute('route');
    }
}
