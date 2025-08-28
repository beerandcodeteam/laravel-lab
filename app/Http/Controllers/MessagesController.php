<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\MessageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MessagesController extends Controller
{

    public function __construct(public MessageService $messageService)
    {
    }

    public function history(User $user)
    {
        try {
            return $this->messageService->getHistory($user);
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
