<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class RequestIdMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $requestId = $request->header('X-Request-ID') ?? Str::uuid();

        $request->attributes->set('request_id', $requestId);

        $response = $next($request);

        $response->header('X-Request-ID', $requestId);

        return $response;
    }
}
