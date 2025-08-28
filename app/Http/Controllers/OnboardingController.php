<?php

namespace App\Http\Controllers;

use App\Http\Requests\OnboardingUpdateEnglishJourneyLogRequest;
use App\Http\Requests\OnboardingUpdateUserRequest;
use App\Models\User;
use App\Services\OnboardingService;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class OnboardingController extends Controller
{

    public function __construct(public OnboardingService $onboardingService)
    {
    }

    public function updateUser(User $user, OnboardingUpdateUserRequest $request)
    {
        try{
            $user = $this->onboardingService->updateUser($user, $request);
            return $user;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => '
                error',
                'message' => 'não foi possível completar a requisição',
            ], 500);
        }
    }

    public function updateEnglishJourneyLog(User $user, OnboardingUpdateEnglishJourneyLogRequest $request)
    {
        try{
            $user = $this->onboardingService->updateEnglishJourneyLog($user, $request);
            return $user;
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => '
                error',
                'message' => 'não foi possível completar a requisição',
            ], 500);
        }
    }

    public function verifyOnboardingInfos(User $user)
    {
        try{
            return $this->onboardingService->verifyOnboardingInfos($user);
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
