<?php

namespace App\Http\Controllers;

use App\Http\Requests\IncomingMessageRequest;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WhatsAppController extends Controller
{

    public function __construct(public UserService $userService)
    {
    }

    public function onboarding(IncomingMessageRequest $request)
    {
        try{
            $user = $this->userService->findOrCreateUserByPhone($request);
            return $user;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'não foi possível completar a requisição',
            ], 500);
        }
    }
}
