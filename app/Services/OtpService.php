<?php

namespace App\Services;

use App\Models\OtpRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Exception;

class OtpService
{
    protected $orangeSmsService;

    public function __construct(OrangeSmsService $orangeSmsService)
    {
        $this->orangeSmsService = $orangeSmsService;
    }

    /**
     * Génère un OTP de 6 chiffres
     */
    public function generateOtp(): string
    {
        return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    }

    /**
     * Demande un OTP pour un numéro de téléphone
     */
    public function requestOtp(string $phone): void
    {
        // Validation du format de téléphone
        if (!$this->isValidPhoneNumber($phone)) {
            throw new Exception('Format de numéro de téléphone invalide');
        }

        // Vérifier si l'utilisateur existe
        $user = User::where('phone', $phone)->first();
        if (!$user) {
            throw new Exception('Utilisateur non trouvé');
        }

        // Générer l'OTP
        $otp = $this->generateOtp();
        $expiresAt = now()->addMinutes(5);

        // Sauvegarder en base
        OtpRequest::create([
            'phone' => $phone,
            'otp' => $otp,
            'expires_at' => $expiresAt,
        ]);

        // Envoyer l'OTP
        $this->sendOtp($phone, $otp);

        // Log en développement
        if (app()->environment('local')) {
            Log::info("OTP généré pour {$phone}: {$otp} (expire le {$expiresAt})");
        }
    }

    /**
     * Envoie l'OTP par SMS
     */
    protected function sendOtp(string $phone, string $otp): void
    {
        $message = "OM Pay: Votre code de vérification est {$otp}. Valable 5 minutes.";

        if (app()->environment('local')) {
            // En développement, on log seulement
            Log::info("SMS OTP simulé vers {$phone}: {$message}");
        } elseif (app()->environment('production')) {
            // En production, envoi réel via Orange SMS
            $result = $this->orangeSmsService->sendSms($phone, $message);
            if (!$result) {
                Log::error("Échec envoi SMS OTP vers {$phone}");
                throw new Exception('Erreur lors de l\'envoi du SMS');
            }
        } else {
            // Pour staging/testing, simulation avec log
            Log::info("SMS OTP simulé (staging) vers {$phone}: {$message}");
        }
    }

    /**
     * Vérifie un OTP
     */
    public function verifyOtp(string $phone, string $otp): bool
    {
        $otpRequest = OtpRequest::valid($phone, $otp)->first();

        if (!$otpRequest) {
            return false;
        }

        // Marquer comme utilisé
        $otpRequest->markAsUsed();

        return true;
    }

    /**
     * Nettoie les OTP expirés
     */
    public function cleanupExpiredOtps(): int
    {
        return OtpRequest::expired()->delete();
    }

    /**
     * Valide le format du numéro de téléphone sénégalais
     */
    protected function isValidPhoneNumber(string $phone): bool
    {
        return preg_match('/^\+2217[5678]\d{7}$/', $phone);
    }
}