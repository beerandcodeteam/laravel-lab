<?php

namespace App\Services;

use App\Http\Requests\IncomingMessageRequest;
use App\Models\User;

class AuthService
{

    public function login(IncomingMessageRequest $request): array
    {
        $user = User::firstOrCreate(
            ['phone' => $request->input('WaId')],
            [
                'name' => $request->input('ProfileName')
            ]
        );

        $user->tokens()->delete();
        $token = $user->createToken($request->input('WaId'));

        return [
            'user' => $user->fresh()->load(['level', 'role', 'preferredFoci']),
            'token' => $token->plainTextToken,
        ];

    }

}
