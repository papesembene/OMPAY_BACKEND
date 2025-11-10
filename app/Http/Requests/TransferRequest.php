<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TransferRequest extends FormRequest
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
            'amount' => 'required|numeric|min:100|max:1000000',
            'recipient_phone' => [
                'required',
                'string',
                'regex:/^(\+221)?(70|71|75|76|77|78)[0-9]{7}$/',
               
            ],
            'description' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'amount.required' => 'Le montant est obligatoire.',
            'amount.numeric' => 'Le montant doit être un nombre.',
            'amount.min' => 'Le montant minimum est de 100 FCFA.',
            'amount.max' => 'Le montant maximum est de 1 000 000 FCFA.',
            'recipient_phone.required' => 'Le numéro du destinataire est obligatoire.',
            'recipient_phone.regex' => 'Le numéro doit être au format +221XXXXXXXXX.',
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
        ];
    }

}