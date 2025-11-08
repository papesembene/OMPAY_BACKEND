<?php

namespace App\Exceptions;

use Exception;

class AuthenticationException extends Exception
{
    public function render($request)
    {
        return response()->json([
            'success' => false,
            'message' => 'Identifiants incorrects.',
            'errors' => [],
            'timestamp' => now()->toIso8601String(),
        ], 401);
    }
}