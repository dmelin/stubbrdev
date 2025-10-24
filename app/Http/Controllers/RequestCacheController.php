<?php

namespace App\Http\Controllers;

use App\Models\RequestCache;
use Illuminate\Http\Request;

class RequestCacheController extends Controller
{
    public function clear(Request $request) {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        RequestCache::where('token', $token)->delete();

        return response(null, 204);
    }
}
