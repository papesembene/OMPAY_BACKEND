<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Contracts\PaymentServiceInterface;
use App\Http\Requests\PaymentRequest;
use App\Http\Requests\TransferRequest;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Exception;

class PaymentController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected PaymentServiceInterface $paymentService
    ) {}

    /**
     * Vérifier les soldes des wallets
     */
    public function checkBalance(): JsonResponse
    {
        $user = auth()->user();
        $wallets = $user->wallets()->get();

        if ($wallets->isEmpty()) {
            return $this->error('Aucun wallet trouvé', 404);
        }

        $totalBalance = $wallets->sum('balance');

        return $this->success([
            'wallets' => $wallets->map(function($wallet) {
                return [
                    'reference' => $wallet->reference,
                    'label' => $wallet->label,
                    'balance' => $wallet->balance,
                    'currency' => $wallet->currency,
                    'is_primary' => $wallet->is_primary
                ];
            }),
            'total_balance' => $totalBalance,
            'currency' => 'XOF'
        ], 'Soldes récupérés avec succès');
    }

    /**
     * Effectuer un paiement vers un marchand
     */
    public function makePayment(PaymentRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $validated = $request->validated();

            // Utiliser le wallet principal par défaut (backward compatibility)
            $wallet = $user->wallets()->where('is_primary', true)->first();

            if (!$wallet) {
                return $this->error('Wallet principal non trouvé. Veuillez contacter le support.', 404);
            }

            $result = $this->paymentService->makePayment($validated, $wallet);

            // Récupérer les infos du destinataire (marchand)
            $merchant = null;
            if (isset($validated['merchant_code'])) {
                $merchant = \App\Models\Marchant::where('code', $validated['merchant_code'])->first();
            } elseif (isset($validated['merchant_phone'])) {
                $merchant = \App\Models\Marchant::where('phone', $validated['merchant_phone'])->first();
            }

            return $this->success([
                'id_transaction' => $result['transaction_id'],
                'montant_signe' => '-' . number_format($validated['amount'], 0, ' ', ' ') . ' FCFA',
                'expediteur' => $user->name,
                'numero_expediteur' => $user->phone,
                'destinataire' => $merchant ? $merchant->name : 'Marchand inconnu',
                'numero_destinataire' => $merchant ? $merchant->phone : null,
                'nouveau_solde' => $result['new_balance'],
                'reference' => $result['reference'],
            ], 'Paiement effectué avec succès');

        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }


    /**
     * Effectuer un transfert vers un autre utilisateur
     */
    public function makeTransfer(TransferRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();
            $validated = $request->validated();

            // Utiliser le wallet principal par défaut (backward compatibility)
            $wallet = $user->wallets()->where('is_primary', true)->first();

            if (!$wallet) {
                return $this->error('Wallet principal non trouvé. Veuillez contacter le support.', 404);
            }

            $result = $this->paymentService->makeTransfer($validated, $wallet);

            // Récupérer les infos du destinataire
            $recipient = \App\Models\User::where('phone', $validated['recipient_phone'])->first();

            return $this->success([
                'id_transaction' => $result['transaction_id'],
                'montant_signe' => '-' . number_format($validated['amount'], 0, ' ', ' ') . ' FCFA',
                'expediteur' => $user->name,
                'numero_expediteur' => $user->phone,
                'destinataire' => $recipient ? $recipient->name : 'Utilisateur inconnu',
                'numero_destinataire' => $validated['recipient_phone'],
                'nouveau_solde' => $result['new_balance'],
                'reference' => $result['reference'],
            ], 'Transfert effectué avec succès');

        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}