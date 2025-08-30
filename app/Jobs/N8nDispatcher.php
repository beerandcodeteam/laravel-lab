<?php

namespace App\Jobs;

use App\Models\Message;
use App\Models\User;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class N8nDispatcher implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public User $user,
        public string $token,
        public Message $message
    )
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info($this->token);
        $this->user->load('lastEnglishJourneyLog', 'lastTest');
        Http::post(config('n8n.webhook_endpoint'), [
            'user' => $this->user,
            'token' => explode("|", $this->token)[1],
            'message' => $this->message
        ]);
    }
}
