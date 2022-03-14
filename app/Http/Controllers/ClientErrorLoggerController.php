<?php

namespace App\Http\Controllers\MemberAPI;

use Illuminate\Http\Request;
use JsonException;

class ClientErrorLoggerController
{
    /**
     * @param  Request       $request
     * @throws JsonException
     */
    public function __invoke(Request $request)
    {
        \Log::error("Client Side Error\n".json_encode($request->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), ['request'=>$request->all()]);
        dev_slack_log()->error("Client Side Error\n".json_encode($request->toArray(), JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT), ['request'=>$request->all()]);
    }
}
