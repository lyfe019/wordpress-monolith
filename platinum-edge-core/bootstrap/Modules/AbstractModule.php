<?php
namespace Platinum\Core\Modules;

abstract class AbstractModule implements ModuleInterface
{
    public function dependencies(): array
    {
        return [];
    }

    abstract public function id(): string;
    abstract public function name(): string;
    abstract public function register(): void;
    abstract public function boot(): void;
}