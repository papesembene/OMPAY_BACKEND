<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GenerateQrRequest;
use App\Services\QrCodeService;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Exception;

class QrCodeController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected QrCodeService $qrCodeService
    ) {}

    /**
     * Génère un QR code de paiement pour l'utilisateur connecté
     */
    public function generatePaymentQr(GenerateQrRequest $request): JsonResponse
    {
        try {
            $user = auth()->user()->load('wallet');

            if (!$user->wallet) {
                return $this->error('Wallet non trouvé. Veuillez contacter le support.', 404);
            }

            $qrCodeBase64 = $this->qrCodeService->generatePaymentQr($user->wallet->id);

            return $this->success([
                'qr_code' => $qrCodeBase64,
                'wallet_id' => $user->wallet->id,
                'format' => 'base64',
                'mime_type' => 'image/png'
            ], 'QR code de paiement généré avec succès');

        } catch (Exception $e) {
            return $this->error('Erreur lors de la génération du QR code: ' . $e->getMessage(), 500);
        }
    }
}