<?php

namespace App\Services;

use App\Http\Requests\LoginRequest;
use App\Models\Message;
use App\Models\User;
use App\RolesEnum;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class MessagesService
{

    public function newMessage(User $user, LoginRequest $request, $fromMessage): Message
    {
        $message = DB::transaction(function() use ($user, $request, $fromMessage) {

            $file = null;
            $mime = null;

            if ($request->has('NumMedia') && $request->get('NumMedia') > 0) {

                $mime = $request->get('MediaContentType0');

                $extension = explode('/', $mime)[1] ?? 'dat';
                $fileName = $request->get('SmsMessageSid') . '.' . $extension;


                Log::info("ARQUIVO:", ['url' => $request->get('MediaUrl0')]);

                $response = Http::withBasicAuth(
                    config('twilio.account_sid'),
                    config('twilio.auth_token'),
                )->get($request->get('MediaUrl0'));

                $file = "messages/{$user->id}/{$fileName}";
                if ($response->successful()) {
                    Log::info("TENTANDO SALVAR");
                    Storage::put("messages/{$user->id}/{$fileName}", $response->body());
                } else {
                    Log::info("TENTANDO SALVAR", $response);
                    throw new \Exception('Falha ao salvar arquivo' . $response->status());
                }

            }

            return Message::create([
                'user_id' => $user->id,
                'from' => $fromMessage,
                'mime' => $mime,
                'type' => $request->get('MessageType'),
                'file' => $file,
                'message' => $request->get('Body') ?? null,
            ]);

        });

        return $message;
    }

}
