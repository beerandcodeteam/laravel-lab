<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class CallController extends Controller
{
    public function __invoke()
    {
        $wsUrl = config('twilio.twilio_ws_url');
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<Response>
  <Say>Connected</Say>
  <Connect>
    <Stream url="{$wsUrl}" />
  </Connect>
  <Say>Disconnected</Say>
</Response>
XML;

        return response($xml, 200)->header('Content-Type', 'text/xml');
    }
}
