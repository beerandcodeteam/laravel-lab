<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('incoming-message', [WhatsAppController::class, 'onboarding'])
    ->middleware([\App\Http\Middleware\TwilioRequestValidator::class])
    ->name('incoming-message');
