<?php
namespace Platinum\Core\Api;

final class HttpResponse
{
    public static function json(array $data, int $status = 200): array
    {
        return [
            'status_code' => $status,
            'headers'     => ['Content-Type' => 'application/json'],
            'body'        => json_encode($data)
        ];
    }

    public static function error(string $message, int $status = 500, array $details = []): array
    {
        $response = [
            'success' => false,
            'error'   => [
                'message' => $message,
                'code'    => $status
            ]
        ];

        if (!empty($details)) {
            $response['error']['details'] = $details;
        }

        return self::json($response, $status);
    }
}