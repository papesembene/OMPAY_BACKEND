<?php

namespace App\Services;

use App\Contracts\AuthServiceInterface;
use App\Exceptions\AuthenticationException;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthService implements AuthServiceInterface
{
    public function login(LoginRequest $request): ?array
    {
        $user = User::where('phone', $request['phone'])->first();

        if (!$user || !Hash::check($request['secret_code'], $user['secret_code'])) {
           throw new AuthenticationException();
        }

        return [
            'user' => $user,
            'token' => $this->generateToken($user),
        ];
    }

    public function generateToken(User $user): string
    {
        return $user->createToken('ompay-app', ['access-app'])->accessToken;
    }
}