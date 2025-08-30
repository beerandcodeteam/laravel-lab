<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class TwilioRequestValidatorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestValidator = new RequestValidator(config('twilio.auth_token'));
        $data = $request->all();

        Log::info([
            'X-Twilio-Signature' => $request->header('X-Twilio-Signature'),
            'data' => $data,
            'url' => config('twilio.twilio_whatsapp_endpoint')
        ]);

        $isValid = $requestValidator->validate(
            $request->header('X-Twilio-Signature'),
            config('twilio.twilio_whatsapp_endpoint'),
            $data
        );

        if (!$isValid)
        {
            return new Response('Não autorizado', 403);
        }

        return $next($request);

    }
}
