<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\IngestEventRequest;
use Throwable;

class EventIngestionController extends Controller
{
    /**
     * @throws Throwable
     */
    public function __invoke(IngestEventRequest $request)
    {
        return response()->json([], 201);
    }
}
