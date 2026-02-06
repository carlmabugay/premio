<?php

namespace App\Providers;

use App\Http\Domain\Events\EventRepository;
use App\Http\Domain\Rewards\RewardRepository;
use App\Http\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use App\Http\Infrastructure\Persistence\Eloquent\EloquentRewardRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepository::class, EloquentEventRepository::class);
        $this->app->bind(RewardRepository::class, EloquentRewardRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
