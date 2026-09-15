<?php

declare(strict_types=1);

namespace App\Http;

use LogicException;

final readonly class Router
{
    /**
     * @param list<array{
     *     method: string,
     *     pattern: string,
     *     controller: object,
     *     action: string
     * }> $routes
     */
    public function __construct(private array $routes) {}

    public function dispatch(): bool
    {
        $requestMethod = $this->requestMethod();
        $requestPath = $this->requestPath();

        foreach ($this->routes as $route) {
            if ($requestMethod !== $route['method']) {
                continue;
            }

            if (preg_match($route['pattern'], $requestPath, $routeMatches) !== 1) {
                continue;
            }

            $routeParameters = array_values(array_slice($routeMatches, 1));

            $this->runController(
                $route['controller'],
                $route['action'],
                $routeParameters,
            );

            return true;
        }

        return false;
    }

    /**
     * @param list<string> $routeParameters
     */
    private function runController(
        object $controller,
        string $action,
        array $routeParameters,
    ): void {
        if (!method_exists($controller, $action)) {
            throw new LogicException(sprintf('Controller method "%s" does not exist.', $action));
        }

        $controller->{$action}(...$routeParameters); // @phpstan-ignore method.dynamicName
    }

    private function requestMethod(): string
    {
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'GET';

        return is_string($requestMethod) ? $requestMethod : 'GET';
    }

    private function requestPath(): string
    {
        $requestUri = $_SERVER['REQUEST_URI'] ?? '/';

        if (!is_string($requestUri)) {
            return '';
        }

        $requestPath = parse_url($requestUri, PHP_URL_PATH);

        return is_string($requestPath) ? $requestPath : '';
    }
}
