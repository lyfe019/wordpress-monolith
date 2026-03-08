<?php
namespace Platinum\Core;

final class Environment
{
    public static function type(): string
    {
        return defined('WP_ENVIRONMENT_TYPE') ? WP_ENVIRONMENT_TYPE : 'production';
    }

    public static function isDebug(): bool
    {
        return defined('WP_DEBUG') && WP_DEBUG === true;
    }
}