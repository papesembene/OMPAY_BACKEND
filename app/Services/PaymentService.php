<?php

namespace App\Services;

use App\Contracts\PaymentServiceInterface;
use App\Contracts\AuthorizationServiceInterface;
use App\Contracts\WalletServiceInterface;
use App\Contracts\TransactionServiceInterface;
use App\Contracts\NotificationServiceInterface;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Exception;

class PaymentService implements PaymentServiceInterface
{
    public function __construct(
        protected AuthorizationServiceInterface $authorizationService,
        protected WalletServiceInterface $walletService,
        protected TransactionServiceInterface $transactionService,
        protected NotificationServiceInterface $notificationService
    ) {}

    /**
     * Effectuer un paiement vers un marchand
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function makePayment(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $wallet = $user->wallet;

            // 1. Vérifier les autorisations
            $this->authorizationService->checkPaymentAuthorization($data);

            // 2. Débiter le wallet
            $ancienSolde = $this->walletService->debit($wallet, $data['amount']);

            // 3. Créer la transaction
            $transaction = $this->transactionService->createPayment($data);

            // 4. Notifier
            $this->notificationService->notifyPayment($transaction);

            return [
                'transaction_id' => $transaction->id,
                'new_balance' => $this->walletService->getBalance($wallet->fresh()),
                'reference' => $transaction->orange_tx_id,
            ];
        });
    }

    /**
     * Effectuer un transfert vers un autre utilisateur
     *
     * @param array $data
     * @return array
     * @throws Exception
     */
    public function makeTransfer(array $data): array
    {
        return DB::transaction(function () use ($data) {
            $user = auth()->user();
            $wallet = $user->wallet;

            // 1. Vérifier les autorisations
            $this->authorizationService->checkTransferAuthorization($data);

            // 2. Vérifier que le destinataire existe
            $recipient = User::where('phone', $data['recipient_phone'])->first();
            if (!$recipient) {
                throw new Exception('Destinataire introuvable');
            }

            // 3. Débiter l'expéditeur
            $this->walletService->debit($wallet, $data['amount']);

            // 4. Créditer le destinataire
            $this->walletService->credit($recipient->wallet, $data['amount']);

            // 5. Créer la transaction
            $transaction = $this->transactionService->createTransfer($data);

            // 6. Notifier
            $this->notificationService->notifyTransfer($transaction);

            return [
                'transaction_id' => $transaction->id,
                'new_balance' => $this->walletService->getBalance($wallet->fresh()),
                'reference' => $transaction->orange_tx_id,
            ];
        });
    }

    /**
     * Générer une référence de transaction unique
     *
     * @return string
     */
    public function generateTransactionReference(): string
    {
        return $this->transactionService->generateReference();
    }
}