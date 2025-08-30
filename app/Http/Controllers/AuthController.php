<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Jobs\N8nDispatcher;
use App\Services\AuthService;
use App\Services\MessagesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    public function __construct(
        public AuthService $authService,
        public MessagesService $messagesService
    )
    {
    }

    public function incomingMessage(LoginRequest $request)
    {
        try {
            $data = $this->authService->login($request);
            $message = $this->messagesService->newMessage($data['user'], $request, 'USER');

            dispatch(new N8nDispatcher($data['user'], $data['token'], $message));

        } catch(\Throwable $e) {
            return response()->json(["message" => "Erro ao processar requisição"], 500);
        }
    }
}
