<?php

use App\Http\Controllers\Api\V1\CollectRewardRuleController;
use App\Http\Controllers\Api\V1\CreateRewardRuleController;
use App\Http\Controllers\Api\V1\EventIngestionController;
use App\Http\Controllers\Api\V1\SelectRewardRuleController;
use App\Http\Middleware\EnsureApiKeyIsValid;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::post('/events', EventIngestionController::class);
    Route::post('/rules', CreateRewardRuleController::class);
    Route::get('/rules', CollectRewardRuleController::class);
    Route::get('/rules/{id}', SelectRewardRuleController::class);
    Route::post('/customers', CreateRewardRuleController::class);
})->middleware(EnsureApiKeyIsValid::class);
