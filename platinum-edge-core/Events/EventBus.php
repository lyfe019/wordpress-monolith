<?php
namespace Platinum\Core\Events;

final class EventBus
{
    private static array $listeners = [];

    /**
     * Subscribe a listener to a specific event.
     * Usually called during Module::boot()
     */
    public static function subscribe(string $event, callable $listener): void
    {
        self::$listeners[$event][] = $listener;
    }

    /**
     * Dispatch an event to all interested subscribers.
     */
    public static function dispatch(string $event, array $payload = []): void
    {
        if (!isset(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $listener) {
            // In a monolith, these are executed synchronously for simplicity,
            // but the interface allows for future async queues.
            call_user_func($listener, $payload);
        }
    }
}