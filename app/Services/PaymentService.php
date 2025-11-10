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
use Illuminate\Support\Facades\Log;

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
           if (!$user) {
            throw new Exception('Utilisateur non authentifié');
        }
        
        Log::info('User ID: ' . $user->id);
        Log::info('Wallet: ', ['wallet' => $user->wallet]);
            $wallet = $user->wallet()->first();
            
            if (!$wallet) {
                throw new Exception('Portefeuille non trouvé. Veuillez contacter le support.');
            }

            // Vérifier que le solde est suffisant
            \Illuminate\Support\Facades\Log::info('Balance check', [
                'balance' => $wallet->balance,
                'amount' => $data['amount'],
                'comparison' => $wallet->balance < $data['amount']
            ]);

            if ($wallet->balance < $data['amount']) {
                throw new Exception('Solde insuffisant. Solde actuel: ' . $wallet->balance . ' FCFA');
            }

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
                'new_balance' => $wallet->balance,
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
        // Récupérer l'utilisateur authentifié
        $user = auth()->user();
        
        if (!$user) {
            throw new Exception('Utilisateur non authentifié');
        }

        // Charger le wallet de l'expéditeur avec sécurité
        $user->loadMissing('wallet');
        $walletSender = $user->wallet;
          
        if (!$walletSender) {
            throw new Exception('Portefeuille expéditeur introuvable.');
        }

        // Vérifier le solde de l'expéditeur
        if ($walletSender->balance < $data['amount']) {
            throw new Exception('Solde insuffisant.');
        }

        // Récupérer le destinataire avec son wallet
        $recipient = User::with('wallet')->where('phone', $data['recipient_phone'])->first();
       
        if (!$recipient) {
            throw new Exception('Destinataire introuvable.');
        }
        $walletRecipient = $recipient->wallet;
        if (!$walletRecipient) {
            throw new Exception('Portefeuille destinataire introuvable.');
        }
        

        // 1. Vérifier les autorisations
        $this->authorizationService->checkTransferAuthorization($data);

        // 2. Débiter l'expéditeur et créditer le destinataire
        $this->walletService->debit($walletSender, $data['amount']);
        $this->walletService->credit($walletRecipient, $data['amount']);

        // 3. Créer la transaction
        $transaction = $this->transactionService->createTransfer($data);
        

        // 4. Notifier
        $this->notificationService->notifyTransfer($transaction);

        // 5. Retourner les infos
        return [
            'transaction_id' => $transaction->id,
            'new_balance' => $walletSender->balance,
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