<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\RequestOtpRequest;
use App\Http\Requests\VerifyOtpRequest;
use App\Models\User;
use App\Services\OtpService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Laravel\Passport\Token;
use Exception;

class AuthController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected OtpService $otpService
    ) {}

    /**
     * Demande un OTP pour l'authentification
     */
    public function requestOtp(RequestOtpRequest $request): JsonResponse
    {
        try {
            $phone = $request->validated()['phone'];
            $this->otpService->requestOtp($phone);

            $response = [
                'message' => 'Code OTP envoyé avec succès',
                'phone' => $phone
            ];

            // En développement OU si paramètre debug=true, inclure l'OTP dans la réponse
            if (app()->environment('local') || request('debug') === 'true') {
                $lastOtp = \App\Models\OtpRequest::where('phone', $phone)
                    ->where('used', false)
                    ->orderBy('created_at', 'desc')
                    ->first();

                if ($lastOtp) {
                    $response['debug_otp'] = $lastOtp->otp;
                    $response['debug_expires_at'] = $lastOtp->expires_at->toISOString();
                    $response['debug_message'] = '⚠️ DEBUG MODE: Ce code ne serait pas visible en production';
                }
            }

            return $this->success($response);

        } catch (Exception $e) {
            Log::error('Erreur demande OTP', [
                'phone' => $request->validated()['phone'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return $this->error(
                'Erreur lors de l\'envoi du code OTP',
                500
            );
        }
    }

    /**
     * Vérifie l'OTP et génère les tokens
     */
    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        try {
            $validated = $request->validated();

            // Vérifier l'OTP
            if (!$this->otpService->verifyOtp($validated['phone'], $validated['otp'])) {
                return $this->error(
                    'Code OTP invalide ou expiré',
                    401
                );
            }

            // Récupérer l'utilisateur
            $user = User::where('phone', $validated['phone'])->first();

            if (!$user) {
                return $this->error(
                    'Utilisateur non trouvé',
                    404
                );
            }

            // Générer les tokens Passport
            $tokenResult = $user->createToken('API Token');
            $token = $tokenResult->token;

            // Configurer l'expiration
            $token->expires_at = now()->addHour(); 
            $token->save();

            return $this->success([
                'access_token' => $tokenResult->accessToken,
                'refresh_token' => $token->id, 
                'token_type' => 'Bearer',
                'expires_in' => 3600, 
            ], 'Authentification réussie');

        } catch (Exception $e) {
            Log::error('Erreur vérification OTP', [
                'phone' => $validated['phone'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);

            return $this->error(
                'Erreur lors de la vérification du code OTP',
                500
            );
        }
    }
}
