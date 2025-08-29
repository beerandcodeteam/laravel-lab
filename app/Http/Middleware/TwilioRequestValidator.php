<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class TwilioRequestValidator
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $requestValidator = new RequestValidator(config('twilio.auth_token'));

        $requestData = $request->toArray();

        $isValid = $requestValidator->validate(
            $request->header('X-Twilio-Signature'),
            config('twilio.twilio_whatsapp_endpoint'),
            $requestData
        );

        if ($isValid) {
            return $next($request);
        } else {
            return new Response('não autorizado', 403);
        }
    }
}
