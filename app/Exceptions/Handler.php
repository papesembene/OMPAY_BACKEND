<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Validation\ValidationException;
use App\Exceptions\AuthenticationException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->renderable(function (Throwable $e, $request) {
            // Uniquement pour les requêtes API
            if ($request->expectsJson() || $request->is('api/*')) {
                return $this->handleApiException($e);
            }
        });
    }

    /**
     * Gère les exceptions pour les réponses API JSON unifiées
     */
    private function handleApiException(Throwable $e)
    {
        // 1. Erreur de validation
        if ($e instanceof ValidationException) {
            return response()->json([
                'success' => false,
                'message' => 'Données invalides.',
                'errors' => $e->errors(),
                'timestamp' => now()->toIso8601String(),
            ], 422);
        }

        // 2. Authentification échouée
        if ($e instanceof AuthenticationException) {
            return response()->json([
                'success' => false,
                'message' => 'Identifiants incorrects.',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 401);
        }

        // 3. 404 Route non trouvée
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\NotFoundHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Route non trouvée.',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 404);
        }

        // 4. 405 Méthode non autorisée
        if ($e instanceof \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException) {
            return response()->json([
                'success' => false,
                'message' => 'Méthode HTTP non autorisée.',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 405);
        }

        // 5. Erreur interne → Masquée en production
        if (!config('app.debug')) {
            return response()->json([
                'success' => false,
                'message' => 'Une erreur est survenue.',
                'errors' => [],
                'timestamp' => now()->toIso8601String(),
            ], 500);
        }

        // 6. Sinon → Laravel par défaut (dev)
        // return parent::render($request, $e);
    }
}