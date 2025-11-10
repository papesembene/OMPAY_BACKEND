<?php

namespace App\Http\Controllers\Api;

use App\Contracts\AuthServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    use ApiResponseTrait;
    public function __construct(
        protected AuthServiceInterface $authService
    ) {}

    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $result = $this->authService->login($request);

            return $this->success([
                'token' => $result['token'],
                'user' => [
                    'id' => $result['user']->id,
                    'name' => $result['user']->name,
                    'masked_phone' => '******' . substr($result['user']->phone, -4),
                ]
            ], 'Connexion réussie');

        } catch (\App\Exceptions\AuthenticationException $e) {
            return $this->error('Identifiants incorrects.', 401);
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();
            return $this->success(null, 'Déconnexion réussie');
        } catch (\Exception $e) {
            return $this->error('Erreur lors de la déconnexion.', 500);
        }
    }
}
