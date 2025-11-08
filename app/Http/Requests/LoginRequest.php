<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'phone' => 'required|string|regex:/^\+2217[567801]\d{7}$/',
            'secret_code' => 'required|string|size:4',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.regex' => 'Numéro Orange Money invalide.',
            'secret_code.size' => 'Le code doit faire 4 chiffres.',
        ];
    }
}