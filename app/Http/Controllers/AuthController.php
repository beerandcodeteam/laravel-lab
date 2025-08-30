<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(public AuthService $authService)
    {
    }

    public function incomingMessage(LoginRequest $request)
    {

        Log::info($request->all());
        try {
            $user = $this->authService->login($request);


        } catch(\Throwable $e) {

        }
    }
}
