<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransactionHistoryRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'page' => 'nullable|integer|min:1',
            'per_page' => 'nullable|integer|min:1|max:100',
            'type' => 'nullable|in:payment,transfer',
            'status' => 'nullable|in:pending,success,failed',
            'date_from' => 'nullable|date|before_or_equal:date_to',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'sort_by' => 'nullable|in:created_at,amount,type',
            'sort_order' => 'nullable|in:asc,desc'
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'per_page.max' => 'Le nombre maximum de transactions par page est de 100.',
            'type.in' => 'Le type doit être soit "payment" soit "transfer".',
            'status.in' => 'Le statut doit être "pending", "success" ou "failed".',
            'sort_by.in' => 'Le tri peut se faire par "created_at", "amount" ou "type".',
            'sort_order.in' => 'L\'ordre de tri doit être "asc" ou "desc".'
        ];
    }
}