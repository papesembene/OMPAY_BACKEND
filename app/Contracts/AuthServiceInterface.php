<?php

namespace App\Contracts;

use App\Http\Requests\LoginRequest;
use App\Models\User;

interface AuthServiceInterface
{
    public function login(LoginRequest $request): ?array;
    public function generateToken(User $user): string;
}