<?php

namespace App\Services;

use App\Http\Requests\OnboardingUpdateEnglishJourneyLogRequest;
use App\Http\Requests\OnboardingUpdateUserRequest;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OnboardingService
{
    public function updateUser(User $user, OnboardingUpdateUserRequest $request)
    {
        Log::info("USUARIOS");
        Log::info($request->all());

        $updateData = [];

        if ($request->filled('name')) {
            $updateData['name'] = $request->name;
        }

        if ($request->filled('daily_target_minutes')) {
            $updateData['daily_target_minutes'] = $request->daily_target_minutes;
        }

        if ($request->filled('preferred_start_time')) {
            $updateData['preferred_start_time'] = $request->preferred_start_time;
        }

        if ($request->filled('preferred_days')) {
            $updateData['preferred_days'] = $request->preferred_days;
        }

        $user->update($updateData);

        return $this->verifyOnboardingInfos($user->fresh());
    }

    public function updateEnglishJourneyLog(User $user, OnboardingUpdateEnglishJourneyLogRequest $request)
    {
        Log::info("JOURNEY LOG");
        Log::info($request->all());

        $data = [];

        if ($request->filled('level_summary')) {
            $data['level_summary'] = $request->level_summary;
        }

        if ($request->filled('difficulties')) {
            $data['difficulties'] = $request->difficulties;
        }

        if ($request->filled('confidence_level')) {
            $data['confidence_level'] = $request->confidence_level;
        }

        if ($request->filled('ia_summary')) {
            $data['ia_summary'] = $request->ia_summary;
        }

        $journey = $user->lastJourneyLog;

        if ($journey) {
            $journey->update($data);
        } else {
            $journey = $user->lastJourneyLog()->create($data);
        }

        return $this->verifyOnboardingInfos($user->fresh());
    }

    public function verifyOnboardingInfos(User $user)
    {
        $user->load('lastJourneyLog:id,english_journey_logs.user_id,level_summary,difficulties,ia_summary,confidence_level');
        $user = $user->only('name',
            'daily_target_minutes',
            'preferred_start_time',
            'preferred_days',
            'lastJourneyLog'
        );

        return $user;

    }
}
