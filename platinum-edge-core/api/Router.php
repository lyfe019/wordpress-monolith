<?php
namespace Platinum\Core\Api;

final class Router
{
    private array $routes = [];

    public function add(Route $route): void
    {
        $this->routes[] = $route;
    }

    /**
     * Helper to see all registered routes for debugging.
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    public function match(HttpRequest $request): ?Route
    {
        $requestPath = $request->path();
        $requestMethod = $request->method();

        foreach ($this->routes as $route) {
            $targetPath = '/' . trim($route->path, '/');

            if (
                $route->method === $requestMethod &&
                $targetPath === $requestPath
            ) {
                return $route;
            }
        }

        return null;
    }
}