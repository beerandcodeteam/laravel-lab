<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\Message;
use App\Models\User;
use App\RolesEnum;
use Illuminate\Support\Facades\DB;

class MessagesService
{

    public function newMessage(User $user, LoginRequest $request, $fromMessage): array
    {
        DB::transaction(function() use ($user, $request, $fromMessage) {

            if ($request->has('NumMedia') && $request->get('NumMedia') > 0) {

                $file = null;

            }

            return Message::create([
                'from' => $fromMessage,
                'mime' => $request->get('MediaContentType0') ?? 'text',
                'type' => $request->get('MessageType'),
                'file' => $file,
                'message' => $request->get('Body'),
            ]);

        });
    }

}
