<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomingMessageRequest;
use App\Services\AuthService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{

    public function __construct(public AuthService $userService)
    {
    }

    public function login(IncomingMessageRequest $request)
    {
        try{
            $user = $this->userService->login($request);
            return $user;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => '
                error',
                'message' => 'não foi possível completar a requisição',
            ], 500);
        }
    }
}
