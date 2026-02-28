<?php

namespace App\Http\Middleware;

use App\Domain\ApiKeys\Services\ApiKeyService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

readonly class EnsureApiKeyIsValid
{
    public function __construct(
        private ApiKeyService $apiKeyService,
    ) {}

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

        $exists = $this->apiKeyService->isKeyExists($key);

        if (! $exists) {
            return response()->json([
                'message' => 'Invalid API key.',
            ], 403);
        }

        return $next($request);
    }
}
