<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
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
            'merchant_code' => 'nullable|string|exists:marchants,code',
            'merchant_phone' => [
                'nullable',
                'string',
                'regex:/^(\+221)?(70|71|75|76|77|78)[0-9]{7}$/',
                'exists:marchants,phone',
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
            'merchant_code.exists' => 'Le code marchand spécifié n\'existe pas.',
            'merchant_phone.regex' => 'Le numéro du marchand doit être au format +221XXXXXXXXX.',
            'merchant_phone.exists' => 'Le numéro du marchand spécifié n\'existe pas.',
            'description.max' => 'La description ne peut pas dépasser 255 caractères.',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            // Vérifier qu'au moins un des champs merchant_code ou merchant_phone est fourni
            if (empty($this->merchant_code) && empty($this->merchant_phone)) {
                $validator->errors()->add('merchant', 'Vous devez fournir soit le code marchand soit le numéro de téléphone du marchand.');
            }

            // Vérifier qu'un seul des deux champs est fourni
            if (!empty($this->merchant_code) && !empty($this->merchant_phone)) {
                $validator->errors()->add('merchant', 'Vous ne pouvez fournir qu\'un seul identifiant de marchand (code ou numéro).');
            }
        });
    }
}