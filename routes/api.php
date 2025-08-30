<?php

use App\Http\Controllers\AuthController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::post('/incoming-message', [AuthController::class, 'incomingMessage'] )
    ->middleware([\App\Http\Middleware\TwilioRequestValidatorMiddleware::class])
    ->name('auth.incoming-message');
