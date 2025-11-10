<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RequestOtpRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'phone' => [
                'required',
                'string',
                'regex:/^\+2217[5678]\d{7}$/',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'phone.required' => 'Le numéro de téléphone est obligatoire',
            'phone.regex' => 'Format de numéro de téléphone invalide. Utilisez le format +2217XXXXXXX',
        ];
    }
}