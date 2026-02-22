<?php

namespace App\Http\Middleware;

use App\Models\ApiKey;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureApiKeyIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = $request->header('X-API-KEY');

        if (! $key) {
            return response()->json([
                'message' => 'API key missing.',
            ], 401);
        }

        $exists = ApiKey::where('key', $key)
            ->where('is_active', true)
            ->exists();

        if (! $exists) {
            return response()->json([
                'message' => 'Invalid API key.',
            ], 403);
        }

        return $next($request);
    }
}
