<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\MessagesController;

Route::post('/incoming-message', [AuthController::class, 'incomingMessage'] )
    ->middleware([\App\Http\Middleware\TwilioRequestValidatorMiddleware::class])
    ->name('auth.incoming-message');

Route::post('incoming-call', \App\Http\Controllers\CallController::class)
    ->middleware([\App\Http\Middleware\TwilioCallRequestValidatorMiddleware::class]);


Route::middleware('auth:sanctum')->group(function () {

    Route::prefix('onboarding')->name('onboarding.')->group(function () {
        Route::put('user/{user}', [OnboardingController::class, 'updateUser'])
            ->name('update-user');
        Route::get('user/{user}', [OnboardingController::class, 'verifyOnboardingInfos'])
            ->name('get-user');
        Route::put('english-journey-log/{user}', [OnboardingController::class, 'updateEnglishJourneyLog'])
            ->name('update-journey-user');
    });

    Route::prefix('messages')->name('messages.')->group(function () {
        Route::get('{user}', [MessagesController::class, 'history'])
            ->name('history');
    });

    Route::prefix('tests')->name('tests.')->group(function () {
        Route::get('user/{user}', [\App\Http\Controllers\TestsController::class, 'verifyTestSituation'])
            ->name('test');
        Route::post('upload-file/question/{question}', [\App\Http\Controllers\TestsController::class, 'uploadFile'])
            ->name('question-upload-file');
        Route::post('upload-file-from-twilio/question/{question}', [\App\Http\Controllers\TestsController::class, 'uploadFileFromTwilio'])
            ->name('twilio-upload-file');
    });

});
