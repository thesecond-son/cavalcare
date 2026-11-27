<?php

namespace App\Support;

use Illuminate\Http\JsonResponse;

/**
 * The one error shape every API response uses (backend spec, section 6.2):
 * { "error": { "code": "shift_taken", "message": "...", "fields": {...} } }
 * "code" is stable and safe for the apps to switch on; "message" is for people.
 */
final class ApiError
{
    public static function make(string $code, string $message, int $status, array $fields = []): JsonResponse
    {
        $error = ['code' => $code, 'message' => $message];
        if ($fields) {
            $error['fields'] = $fields;
        }

        return response()->json(['error' => $error], $status);
    }
}
