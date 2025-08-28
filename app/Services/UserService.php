<?php

namespace App\Services;

use App\Http\Requests\IncomingMessageRequest;
use App\Models\User;

class UserService
{

    public function findOrCreateUserByPhone(IncomingMessageRequest $request): array
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
            'user' => $user->fresh()->load(['level', 'userPreference', 'role', 'latestPlacementTest', 'preferredFoci']),
            'token' => $token->plainTextToken,
        ];

    }

}
