<?php

namespace App\Services;

use App\Http\Requests\OnboardingUpdateEnglishJourneyLogRequest;
use App\Http\Requests\OnboardingUpdateUserRequest;
use App\Models\Question;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class TestService
{
    public function verifyOnboardingInfos(User $user)
    {
        $user->load('lastJourneyLog:id,english_journey_logs.user_id,level_summary,difficulties,ia_summary,confidence_level');

        $user->load('lastLessonTest.questions');

        $user = $user->only('name',
            'daily_target_minutes',
            'preferred_start_time',
            'preferred_days',
            'lastJourneyLog',
            'lastLessonTest'
        );

        return $user;

    }

    public function uploadFile(Question $question, Request $request)
    {
        $base_64 = base64_decode($request->data);

        $path = 'listening/listening-' . $question->id . '.mp3';
        Storage::put($path, $base_64);

        $question->question_audio_path = $path;
        $question->save();

        $temporaryUrl = Storage::temporaryUrl($path, now()->addMinutes(5));

        return $temporaryUrl;
    }


    public function uploadFileFromTwilio(Question $question, Request $request)
    {

        $response = Http::withBasicAuth(config('twilio.account_sid'), config('twilio.auth_token'))
            ->get($request->mediaUrl);

        $path = 'answer-' . $question->id . '.mp3';
        Storage::put($path, $response->body());

        $question->answer_path = $path;
        $question->save();

        return '/n8n/' . $path;
    }
}
