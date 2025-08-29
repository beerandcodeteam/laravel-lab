<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\User;
use App\Services\TestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class TestsController extends Controller
{

    public function __construct(public TestService $testService)
    {
    }

    public function verifyTestSituation(User $user)
    {

        try{
            return $this->testService->verifyOnboardingInfos($user);
        } catch (\Throwable $e) {
            Log::error($e->getMessage());

            return response()->json([
                'status' => '
                error',
                'message' => 'não foi possível completar a requisição',
            ], 500);
        }

    }

    public function uploadFile(Question $question, Request $request)
    {
        return $this->testService->uploadFile($question, $request);
    }

    public function uploadFileFromTwilio(Question $question, Request $request)
    {
        return $this->testService->uploadFileFromTwilio($question, $request);
    }
}
