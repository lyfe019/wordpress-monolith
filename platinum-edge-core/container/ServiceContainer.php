<?php
namespace Platinum\Core\Container;

final class ServiceContainer
{
    private static ?self $instance = null;
    private array $definitions = [];
    private array $instances = [];
    private bool $locked = false;

    private function __construct() {}

    public static function getInstance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * BDD Reset: Clears everything to prevent state leakage between scenarios.
     */
    public static function reset(): void
    {
        if (self::$instance !== null) {
            self::$instance->instances = [];
            self::$instance->definitions = [];
            self::$instance->locked = false;
            self::$instance = null;
        }
    }

    public function singleton(string $id, callable $factory): void
    {
        $this->assertUnlocked();
        $this->definitions[$id] = ['factory' => $factory, 'shared' => true];
    }

    public function factory(string $id, callable $factory): void
    {
        $this->assertUnlocked();
        $this->definitions[$id] = ['factory' => $factory, 'shared' => false];
    }

    public function get(string $id): mixed
    {
        if (isset($this->instances[$id])) {
            return $this->instances[$id];
        }

        if (!isset($this->definitions[$id])) {
            throw new ContainerException("Service not found: {$id}");
        }

        $definition = $this->definitions[$id];
        $object = ($definition['factory'])($this);

        if ($definition['shared']) {
            $this->instances[$id] = $object;
        }

        return $object;
    }

    public function has(string $id): bool
    {
        return isset($this->definitions[$id]) || isset($this->instances[$id]);
    }

    public function lock(): void
    {
        $this->locked = true;
    }

    private function assertUnlocked(): void
    {
        if ($this->locked) {
            throw new ContainerException('Container is locked.');
        }
    }
}