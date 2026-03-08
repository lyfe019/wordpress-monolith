<?php
namespace Platinum\Core\Modules;

interface ModuleInterface
{
    public function id(): string;
    public function name(): string;
    public function dependencies(): array;
    public function register(): void;
    public function boot(): void;
}