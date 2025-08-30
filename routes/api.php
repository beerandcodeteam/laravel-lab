<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\MessagesController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('incoming-message', [AuthController::class, 'login'])
    ->middleware([\App\Http\Middleware\TwilioRequestValidator::class])
    ->name('incoming-message');

Route::post('incoming-call', \App\Http\Controllers\CallController::class)
    ->middleware([\App\Http\Middleware\TwilioRequestValidator::class]);


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::put('user/{user}', [OnboardingController::class, 'updateUser'])
            ->name('update-user');
        Route::get('user/{user}', [OnboardingController::class, 'verifyOnboardingInfos'])
            ->name('get-user');
        Route::put('english-journey-log/{user}', [OnboardingController::class, 'updateEnglishJourneyLog'])
            ->name('update-user');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('{user}', [MessagesController::class, 'history'])
            ->name('history');
    });

    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('user/{user}', [\App\Http\Controllers\TestsController::class, 'verifyTestSituation'])
            ->name('test');
        Route::post('upload-file/question/{question}', [\App\Http\Controllers\TestsController::class, 'uploadFile'])
            ->name('upload-file');
        Route::post('upload-file-from-twilio/question/{question}', [\App\Http\Controllers\TestsController::class, 'uploadFileFromTwilio'])
            ->name('upload-file');
    });


});

