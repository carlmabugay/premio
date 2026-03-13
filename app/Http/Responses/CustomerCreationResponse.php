<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

readonly class CustomerCreationResponse
{
    public function make(): JsonResponse
    {
        return response()->json([
            'status' => 'processed',
        ], 200);
    }
}
