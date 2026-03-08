<?php
namespace Platinum\Core\Api;

final class HttpResponse
{
    public static function json(array $data, int $status = 200): array
    {
        return [
            'status' => $status,
            'headers' => ['Content-Type' => 'application/json'],
            'body' => $data
        ];
    }

    public static function error(string $message, int $status = 400): array
    {
        return self::json(['error' => true, 'message' => $message], $status);
    }
}