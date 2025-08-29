<?php

namespace App\Services;

use Amp\Websocket\Client\WebsocketConnection;
use Amp\Websocket\Client\WebsocketHandshake;
use Illuminate\Support\Facades\Log;
use function Amp\async;
use function Amp\Websocket\Client\connect;

class OpenAiRealTimeService
{
    private ?WebsocketConnection $connection = null;

    private $onAudioDelta = null;

    public function connect()
    {
        if ($this->connection) {
            return;
        }

        $handshake = new WebsocketHandshake(config('openai.realtime_url'))
            ->withHeader('Authorization', 'Bearer ' . config('openai.api_key'))
            ->withHeader('OpenAI-Beta', 'realtime=v1');

        $this->connection = connect($handshake);
        $prompt = file_get_contents(resource_path('prompts/interview-prompt.txt'));
        // Configura a sessão com parâmetros básicos
        $this->send([
            'type' => 'session.update',
            'session' => [
                'modalities' => ['text', 'audio'],
                'turn_detection' => ['type' => 'server_vad', "silence_duration_ms" => 500, 'prefix_padding_ms' => 300],
                'voice' => 'alloy',
                'input_audio_format' => 'g711_ulaw',
                'output_audio_format' => 'g711_ulaw',
                'instructions' => $prompt,
            ],
        ]);

        async(function () {
            $this->receiveLoop();
        });
    }

    private function receiveLoop(): void
    {
        if (!$this->connection) return;

        try {
            foreach ($this->connection as $message) {
                $raw = (string) $message;
                $event = json_decode($raw, true);

                if (!\is_array($event)) {
                    continue;
                }

                switch ($event['type'] ?? null) {
                    case 'response.audio.delta':
                        if ($this->onAudioDelta) {
                            ($this->onAudioDelta)($event['delta'] ?? '');
                        }
                        break;
                }
            }
        } catch (\Throwable $e) {
            Log::error('OpenAI Realtime error: ' . $e->getMessage());
        } finally {
            $this->close();
        }
    }

    public function isOpen(): bool
    {
        return $this->connection !== null && !$this->connection->isClosed();
    }

    public function close(): void
    {
        try {
            $this->connection?->close();
        } catch (\Throwable) {}
        $this->connection = null;
    }

    /** Recebe audio delta (g711_ulaw base64) */
    public function onAudioDelta(callable $cb): void
    {
        $this->onAudioDelta = $cb;
    }

    /** Envia um pedaço de áudio (base64 g711_ulaw) */
    public function appendAudioBase64(string $base64): void
    {
        if (!$this->isOpen()) return;

        $this->send([
            'type' => 'input_audio_buffer.append',
            'audio' => $base64,
        ]);
    }

    private function send(array $payload): void
    {
        if (!$this->connection || $this->connection->isClosed()) return;

        try {
            $this->connection->sendText(json_encode($payload, JSON_UNESCAPED_UNICODE));
        } catch (\Throwable $e) {
            Log::info('Realtime send error: ' . $e->getMessage());
        }
    }
}
