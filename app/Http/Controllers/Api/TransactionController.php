<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\TransactionHistoryRequest;
use App\Models\Transaction;
use App\Http\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Exception;

class TransactionController extends Controller
{
    use ApiResponseTrait;

    /**
     * Récupérer l'historique complet des transactions avec pagination et filtres
     */
    public function history(TransactionHistoryRequest $request): JsonResponse
    {
        try {
            $user = auth()->user();

            $validated = $request->validated();

            $query = Transaction::with(['marchant', 'wallet'])
                ->where('user_id', $user->id);

            // Appliquer les filtres avec les scopes (code propre et maintenable)
            $query->when($validated['type'] ?? null, fn($q) => $q->ofType($validated['type']))
                  ->when($validated['status'] ?? null, fn($q) => $q->ofStatus($validated['status']))
                  ->when($validated['date_from'] ?? null || $validated['date_to'] ?? null,
                         fn($q) => $q->inDateRange($validated['date_from'] ?? null, $validated['date_to'] ?? null))
                  ->sorted($validated['sort_by'] ?? 'created_at', $validated['sort_order'] ?? 'desc');

            // Pagination
            $perPage = $validated['per_page'] ?? 20;
            $transactions = $query->paginate($perPage);

            // Récupérer les paramètres de tri pour la réponse
            $sortBy = $validated['sort_by'] ?? 'created_at';
            $sortOrder = $validated['sort_order'] ?? 'desc';

            // Formater les données
            $formattedTransactions = $transactions->items();
            $formattedTransactions = collect($formattedTransactions)->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'reference' => $transaction->orange_tx_id,
                    'recipient_phone' => $transaction->recipient_phone,
                    'merchant' => $transaction->marchant ? [
                        'id' => $transaction->marchant->id,
                        'name' => $transaction->marchant->name,
                        'code' => $transaction->marchant->code
                    ] : null,
                    'created_at' => $transaction->created_at->toISOString(),
                    'updated_at' => $transaction->updated_at->toISOString()
                ];
            });

            return $this->success([
                'transactions' => $formattedTransactions->items(),
                'pagination' => [
                    'current_page' => $formattedTransactions->currentPage(),
                    'last_page' => $formattedTransactions->lastPage(),
                    'per_page' => $formattedTransactions->perPage(),
                    'total' => $formattedTransactions->total(),
                    'from' => $formattedTransactions->firstItem(),
                    'to' => $formattedTransactions->lastItem()
                ],
                'filters' => [
                    'type' => $request->validated()['type'] ?? null,
                    'status' => $request->validated()['status'] ?? null,
                    'date_from' => $request->validated()['date_from'] ?? null,
                    'date_to' => $request->validated()['date_to'] ?? null,
                    'sort_by' => $sortBy,
                    'sort_order' => $sortOrder
                ]
            ], 'Historique des transactions récupéré avec succès');

        } catch (Exception $e) {
            return $this->error(
                'Erreur lors de la récupération de l\'historique',
                500
            );
        }
    }

    /**
     * Récupérer les transactions récentes (10 dernières par défaut)
     */
    public function recent(): JsonResponse
    {
        try {
            $user = auth()->user();

            $transactions = Transaction::with(['marchant', 'wallet'])
                ->where('user_id', $user->id)
                ->recent(10) // Utilisation du scope pour un code plus propre
                ->get();

            // Formater les données
            $formattedTransactions = $transactions->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'type' => $transaction->type,
                    'amount' => $transaction->amount,
                    'status' => $transaction->status,
                    'reference' => $transaction->orange_tx_id,
                    'recipient_phone' => $transaction->recipient_phone,
                    'merchant' => $transaction->marchant ? [
                        'id' => $transaction->marchant->id,
                        'name' => $transaction->marchant->name,
                        'code' => $transaction->marchant->code
                    ] : null,
                    'created_at' => $transaction->created_at->toISOString(),
                    'updated_at' => $transaction->updated_at->toISOString()
                ];
            });

            return $this->success([
                'transactions' => $formattedTransactions,
                'count' => $formattedTransactions->count()
            ], 'Transactions récentes récupérées avec succès');

        } catch (Exception $e) {
            return $this->error(
                'Erreur lors de la récupération des transactions récentes',
                500
            );
        }
    }
}