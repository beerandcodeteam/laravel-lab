<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\RolesEnum;
use Illuminate\Support\Facades\DB;

class AuthService
{

    public function login(LoginRequest $request): array
    {
        [$user, $token] = DB::transaction(function() use($request) {

            $user = User::firstOrCreate([
                'phone' => $request->input('WaId')
            ],
            [
                'name' => $request->input('ProfileName'),
                'role_id' => RolesEnum::STUDENT
            ]);

            $user->tokens()->delete();
            $token = $user->createToken("token-user-" . $request->input('WaId'))->accessToken;

            return [$user, $token];

        });

        return [
            'user' => $user,
            'token' => $token,
        ];
    }

}
