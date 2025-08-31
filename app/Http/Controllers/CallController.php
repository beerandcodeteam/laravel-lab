<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CallController extends Controller
{
    public function __invoke(Request $request)
    {

        $wsUrl = config('twilio.twilio_ws_url');
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <Connect>
    <Stream url="{$wsUrl}" />
  </Connect>
  <Say>Disconnected</Say>
</Response>
XML;

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
