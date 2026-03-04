<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;

class CustomerCreationResponse
{
    public function make(): JsonResponse
    {
        return response()->json([
            'status' => 'processed',
        ], 200);
    }
}
