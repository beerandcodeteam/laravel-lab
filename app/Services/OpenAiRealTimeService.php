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
    private $onUserTranscription = null;
    private $onAssistantMessage = null;

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

        // Configura a sessão
        $this->send([
            'type' => 'session.update',
            'session' => [
                'modalities' => ['text', 'audio'],
                'turn_detection' => ['type' => 'server_vad', 'silence_duration_ms' => 500, 'prefix_padding_ms' => 300],
                'voice' => 'alloy',
                'input_audio_format' => 'g711_ulaw',
                'output_audio_format' => 'g711_ulaw',
                'input_audio_transcription' => ['model' => 'whisper-1'], // Para transcrever o usuário
                'instructions' => $prompt,
                'tools' => [
                    ''
                ]
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

                $this->handleEvent($event);
            }
        } catch (\Throwable $e) {
            Log::error('OpenAI Realtime error: ' . $e->getMessage());
        } finally {
            $this->close();
        }
    }

    private function handleEvent(array $event): void
    {
        $type = $event['type'] ?? null;

        switch ($type) {
            case 'response.audio.delta':
                if ($this->onAudioDelta) {
                    ($this->onAudioDelta)($event['delta'] ?? '');
                }
                break;

            case 'conversation.item.input_audio_transcription.completed':
                if ($this->onUserTranscription) {
                    $transcript = $event['transcript'] ?? '';
                    if (!empty(trim($transcript))) {
                        Log::info('User transcript', ['text' => $transcript]);
                        ($this->onUserTranscription)($transcript);
                    }
                }
                break;

            case 'response.done':
                if ($this->onAssistantMessage) {
                    $response = $event['response'] ?? [];
                    $output = $response['output'] ?? [];

                    foreach ($output as $item) {
                        if (isset($item['content'])) {
                            foreach ($item['content'] as $content) {
                                if ($content['type'] === 'audio' && isset($content['transcript'])) {
                                    $transcript = $content['transcript'];
                                    if (!empty(trim($transcript))) {
                                        ($this->onAssistantMessage)($transcript);
                                    }
                                }
                            }
                        }
                    }
                }
                break;

            case 'error':
                Log::error('OpenAI Error', ['error' => $event['error'] ?? []]);
                break;
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

    // Callbacks simples
    public function onAudioDelta(callable $cb): void
    {
        $this->onAudioDelta = $cb;
    }

    public function onUserTranscription(callable $cb): void
    {
        $this->onUserTranscription = $cb;
    }

    public function onAssistantMessage(callable $cb): void
    {
        $this->onAssistantMessage = $cb;
    }

    // Envia áudio do usuário
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
            Log::error('Erro ao enviar para OpenAI: ' . $e->getMessage());
        }
    }
}
