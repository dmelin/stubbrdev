<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ThrottleTokenRequests
{
    public function handle(Request $request, Closure $next)
    {
        $ip = $request->ip();
        $key = "token_request_throttle_{$ip}";

        // Check if IP has made a request recently
        if (Cache::has($key)) {
            $lastRequest = Cache::get($key);
            $secondsRemaining = 10 - now()->diffInSeconds($lastRequest);

            return response()->json([
                'error' => 'Whoa there! You can only request a token once every 10 seconds.',
                'retry_after' => max(1, $secondsRemaining) . ' seconds'
            ], 429);
        }

        // Store the timestamp of this request
        Cache::put($key, now(), now()->addSeconds(10));

        return $next($request);
    }
}