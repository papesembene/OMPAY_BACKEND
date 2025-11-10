<?php

namespace App\Services;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class QrCodeService
{
    /**
     * Génère un QR code de paiement pour l'utilisateur connecté
     *
     * @param string $walletId
     * @return string
     */
    public function generatePaymentQr(string $walletId): string
    {
        try {
            // Créer les données du QR code
            $qrData = json_encode([
                'wallet_id' => $walletId,
                'type' => 'payment',
                'timestamp' => now()->toISOString()
            ]);

            Log::info('Génération QR code paiement', [
                'wallet_id' => $walletId,
                'data_length' => strlen($qrData)
            ]);

            // Générer le QR code en base64
            $qrCode = QrCode::format('png')
                ->size(300)
                ->margin(4)
                ->generate($qrData);

            // Convertir en base64
            $base64Qr = base64_encode($qrCode);

            Log::info('QR code paiement généré avec succès', [
                'wallet_id' => $walletId,
                'base64_length' => strlen($base64Qr)
            ]);

            return $base64Qr;

        } catch (\Exception $e) {
            Log::error('Erreur génération QR code paiement', [
                'wallet_id' => $walletId,
                'error' => $e->getMessage()
            ]);

            throw $e;
        }
    }
}