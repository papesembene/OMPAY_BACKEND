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
     * Vérifier le solde du wallet
     */
    public function checkBalance(): JsonResponse
    {
        $user = auth()->user();
        $wallet = $user->wallet;

        if (!$wallet) {
            return $this->error('Wallet non trouvé', 404);
        }

        return $this->success([
            'balance' => $wallet->balance,
            'currency' => 'FCFA'
        ], 'Solde récupéré avec succès');
    }

    /**
     * Effectuer un paiement vers un marchand
     */
    public function makePayment(PaymentRequest $request): JsonResponse
    {
        try {
            $result = $this->paymentService->makePayment($request->validated());

            return $this->success([
                'transaction_id' => $result['transaction_id'],
                'new_balance' => $result['new_balance'],
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
            $result = $this->paymentService->makeTransfer($request->validated());

            return $this->success([
                'transaction_id' => $result['transaction_id'],
                'new_balance' => $result['new_balance'],
                'reference' => $result['reference'],
            ], 'Transfert effectué avec succès');

        } catch (Exception $e) {
            return $this->error($e->getMessage(), 400);
        }
    }
}