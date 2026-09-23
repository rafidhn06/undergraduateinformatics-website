<?php

namespace App\Support;

class ApiResponse
{
    public static function success(mixed $data, ?array $meta = null): array
    {
        $payload = [
            'status' => 'success',
            'data' => $data,
        ];

        if ($meta !== null) {
            $payload['meta'] = $meta;
        }

        return $payload;
    }

    public static function error(string $message, ?array $errors = null): array
    {
        $payload = [
            'status' => 'error',
            'message' => $message,
        ];

        if ($errors !== null) {
            $payload['errors'] = $errors;
        }

        return $payload;
    }
}
