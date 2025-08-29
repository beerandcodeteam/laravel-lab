<?php

namespace App\Services;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Illuminate\Support\Facades\Log;

class TwilioCallHandlerService implements WebsocketClientHandler
{
    public function handleClient(
        WebsocketClient $client,
        Request         $request,
        Response        $response,
    ): void
    {
        // Aceita apenas no path /call
        if ($request->getUri()->getPath() !== '/call') {
            $client->close();
            return;
        }

        Log::info("Nova chamada recebida");

        $realtime = new OpenAiRealTimeService();

        try {
            foreach ($client as $message) {
                $raw = (string)$message;
                $data = json_decode($raw, true);

                if (!is_array($data)) {
                    continue;
                }

                $event = $data['event'] ?? '';

                switch ($event) {
                    case 'start':
                        $streamSid = $data['start']['streamSid'] ?? null;
                        Log::info("Twilio START streamSid={$streamSid}");

                        // Conecta ao OpenAI Realtime
                        $realtime->connect();

                        // Configura callback para enviar áudio da OpenAI para Twilio
                        $realtime->onAudioDelta(function (string $delta) use ($client, $streamSid) {
                            $client->sendText(json_encode([
                                'event' => 'media',
                                'streamSid' => $streamSid,
                                'media' => ['payload' => $delta],
                            ]));
                        });

                        break;

                    case 'media':
                        $payloadB64 = $data['media']['payload'] ?? '';
                        if ($payloadB64 !== '') {
                            // Envia áudio da Twilio para OpenAI
                            $realtime->appendAudioBase64($payloadB64);
                        }
                        break;

                    case 'stop':
                        $realtime->close();
                        $client->close();
                        break 2;
                }
            }
        } finally {
            $realtime->close();
        }
    }
}
