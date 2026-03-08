<?php
namespace Platinum\Core\Api;

final class Route
{
    /**
     * @param string $method
     * @param string $path
     * @param callable $handler  <-- We keep it callable in logic, but change the type hint below
     * @param array $middleware
     */
    public function __construct(
        public string $method,
        public string $path,
        public $handler, // Removed 'callable' type hint here to fix the PHP Fatal Error
        public array $middleware = []
    ) {}
}