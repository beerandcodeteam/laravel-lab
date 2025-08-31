<?php

namespace App\Services;

use Amp\Http\Server\Request;
use Amp\Http\Server\Response;
use Amp\Websocket\Server\WebsocketClientHandler;
use Amp\Websocket\WebsocketClient;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Twilio\Security\RequestValidator;

class TwilioCallHandlerService implements WebsocketClientHandler
{
    private ?string $callSid = null;
    private ?string $from = null;
    private array $transcription = [];
    private string $audioData = '';

    public function handleClient(
        WebsocketClient $client,
        Request         $request,
        Response        $response,
    ): void
    {
        $realtime = new OpenAiRealTimeService();
        $requestValidator = new RequestValidator(config('twilio.auth_token'));
        parse_str($request->getUri()->getQuery() ?? '', $params);

        $isValid = $requestValidator->validate(
            $request->getHeaders()['x-twilio-signature'][0],
            config('twilio.twilio_ws_url'),
            $params
        );

        if (!$isValid) {
            $client->close();
            return;
        }

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
                        $this->handleStart($data, $realtime, $client);
                        break;

                    case 'media':
                        $payloadB64 = $data['media']['payload'] ?? '';
                        if ($payloadB64 !== '') {
                            // Salva o áudio do usuário
                            $this->audioData .= $payloadB64;
                            // Envia para OpenAI
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
            $this->saveCallData();
        }
    }

    private function handleStart(array $data, OpenAiRealTimeService $realtime, WebsocketClient $client): void
    {
        $start = $data['start'] ?? [];
        $streamSid = $start['streamSid'] ?? null;

        // Pega informações básicas
        $this->callSid = $start['callSid'] ?? null;
        $this->from = $start['customParameters']['From'] ?? $start['from'] ?? 'Desconhecido';

        Log::info("Chamada de: {$this->callSid}",  $data);

        $realtime->connect();

        // Callback para receber transcrição do usuário
        $realtime->onUserTranscription(function (string $transcript) {
            Log::info('Salvando transcrição do usuário', ['text' => $transcript]);
            $this->addToTranscription('USUÁRIO', $transcript);
        });

        // Callback para receber texto do assistente
        $realtime->onAssistantMessage(function (string $message) {
            Log::info('Salvando mensagem do assistente', ['text' => $message]);
            $this->addToTranscription('ASSISTENTE', $message);
        });

        $realtime->onAudioDelta(function (string $delta) use ($client, $streamSid) {
            $this->audioData .= $delta;
            $client->sendText(json_encode([
                'event' => 'media',
                'streamSid' => $streamSid,
                'media' => ['payload' => $delta],
            ]));
        });
    }

    private function addToTranscription(string $speaker, string $text): void
    {
        if (empty(trim($text))) {
            return;
        }

        $this->transcription[] = [
            'time' => now()->format('H:i:s'),
            'speaker' => $speaker,
            'text' => trim($text)
        ];

        Log::info('Transcrição adicionada', [
            'speaker' => $speaker,
            'text' => substr($text, 0, 100) . (strlen($text) > 100 ? '...' : ''),
            'total_entries' => count($this->transcription)
        ]);
    }

    private function saveCallData(): void
    {
        if (!$this->callSid || empty($this->transcription)) {
            return;
        }

        $fileName = "call_{$this->callSid}_" . now()->format('Y-m-d_H-i-s');

        $transcriptText = "";

        foreach ($this->transcription as $entry) {
            $transcriptText .= "[{$entry['time']}] {$entry['speaker']}: {$entry['text']}\n\n";
        }

        Storage::put("calls/{$fileName}_transcricao.txt", $transcriptText);


        Log::info("Dados salvos", [
            'call_sid' => $this->callSid,
            'transcription_file' => "{$fileName}_transcricao.txt",
        ]);
    }
}
