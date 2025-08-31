<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Twilio\Security\RequestValidator;

class TwilioCallRequestValidatorMiddleware
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

        $isValid = $requestValidator->validate(
            $request->header('X-Twilio-Signature'),
            config('twilio.twilio_call_endpoint'),
            $data
        );

        if (!$isValid)
        {
            return new Response('Não autorizado', 403);
        }

        return $next($request);

    }
}
