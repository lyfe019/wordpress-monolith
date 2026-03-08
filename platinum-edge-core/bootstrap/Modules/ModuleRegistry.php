<?php
namespace Platinum\Core\Modules;

final class ModuleRegistry
{
    private static array $capabilities = [];

    public static function register(string $intent, callable $handler): void
    {
        self::$capabilities[$intent] = $handler;
    }

    public static function call(string $intent, array $payload = []): mixed
    {
        if (!isset(self::$capabilities[$intent])) {
            throw new \RuntimeException("Capability [{$intent}] not registered.");
        }

        return call_user_func(self::$capabilities[$intent], $payload);
    }
}