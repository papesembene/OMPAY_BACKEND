<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $signedAmount = $this->getSignedAmount();
        $sender = $this->getSenderInfo();
        $recipient = $this->getRecipientInfo();
        $description = $this->getTransactionDescription();

        return [
            'id' => $this->id,
            'signed_amount' => $signedAmount,
            'sender' => $sender,
            'recipient' => $recipient,
            'description' => $description,
            'status' => $this->status,
            'reference' => $this->orange_tx_id,
            'created_at' => $this->created_at->toISOString(),
            'updated_at' => $this->updated_at->toISOString(),
        ];
    }

    /**
     * Get the signed amount (+ for credit, - for debit)
     */
    private function getSignedAmount(): string
    {
        $amount = $this->amount;
        $isCredit = in_array($this->type, ['credit', 'payment_received', 'transfer_received']);

        return ($isCredit ? '+' : '-') . number_format($amount, 0, ',', ' ') . ' FCFA';
    }

    /**
     * Get sender information
     */
    private function getSenderInfo(): string
    {
        if ($this->type === 'payment') {
            // Paiement vers marchand - l'expéditeur est l'utilisateur actuel
            return $this->user->name . ' (' . $this->user->phone . ')';
        } elseif ($this->type === 'transfer') {
            // Transfert sortant - l'expéditeur est l'utilisateur actuel
            return $this->user->name . ' (' . $this->user->phone . ')';
        } elseif ($this->type === 'transfer_received') {
            // Transfert entrant - l'expéditeur est le destinataire original
            return 'Utilisateur inconnu (' . $this->recipient_phone . ')';
        }

        return 'Système';
    }

    /**
     * Get recipient information
     */
    private function getRecipientInfo(): string
    {
        if ($this->type === 'payment') {
            // Paiement vers marchand
            if ($this->marchant) {
                return $this->marchant->name . ' (' . $this->marchant->code . ')';
            }
            return 'Marchand inconnu (' . ($this->recipient_phone ?? 'N/A') . ')';
        } elseif ($this->type === 'transfer') {
            // Transfert sortant
            return 'Utilisateur inconnu (' . $this->recipient_phone . ')';
        } elseif ($this->type === 'transfer_received') {
            // Transfert entrant - le destinataire est l'utilisateur actuel
            return $this->user->name . ' (' . $this->user->phone . ')';
        }

        return 'Système';
    }

    /**
     * Get transaction description
     */
    private function getTransactionDescription(): string
    {
        if ($this->type === 'payment') {
            return 'Paiement effectué vers ' . ($this->marchant ? $this->marchant->name : 'un marchand');
        } elseif ($this->type === 'transfer') {
            return 'Transfert envoyé vers ' . $this->recipient_phone;
        } elseif ($this->type === 'transfer_received') {
            return 'Transfert reçu de ' . $this->recipient_phone;
        } elseif ($this->type === 'payment_received') {
            return 'Paiement reçu de ' . ($this->marchant ? $this->marchant->name : 'un marchand');
        }

        return 'Transaction ' . $this->type;
    }
}
