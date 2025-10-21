<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use App\Models\ApiKey;

class AdaptiveThrottle
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        if (!$token) {
            return response()->json([
                'error' => 'No API token provided. Did you forget it at home?'
            ], 401);
        }

        $apiKey = ApiKey::where('token', $token)->first();

        if (!$apiKey) {
            return response()->json([
                'error' => 'Invalid API token. This is not the token we\'re looking for.'
            ], 401);
        }

        if (!$apiKey->enabled) {
            return response()->json([
                'error' => 'API token not verified yet. Check your email and verify first!'
            ], 403);
        }

        // Use token as identifier instead of IP
        $key = "api_throttle_{$token}";

        $now = now();
        $throttle = Cache::get($key, [
            'windowStart' => $now,
            'windowSize' => 1000,
            'requestCount' => 0,
        ]);

        $withinWindow = $now->diffInMilliseconds($throttle['windowStart']) < $throttle['windowSize'];

        if ($withinWindow && $throttle['requestCount'] >= 10) {
            return response()->json([
                'error' => 'Too many requests — slow down, speed racer!'
            ], 429);
        }

        $throttle['requestCount'] = $withinWindow
            ? $throttle['requestCount'] + 1
            : 1;

        $throttle['windowStart'] = $withinWindow
            ? $throttle['windowStart']
            : $now;

        // Extract sleep from request body
        $sleepMs = 1000;
        $body = $request->json()->all();
        if (!empty($body['__instructions']['delay'])) {
            $sleepMs = min((int) $body['__instructions']['delay'], 5000);
        }

        $throttle['windowSize'] = $sleepMs;

        Cache::put($key, $throttle, now()->addSeconds(10));

        // Attach API key to request for use in controllers
        $request->merge(['api_key' => $apiKey]);

        return $next($request);
    }
}
