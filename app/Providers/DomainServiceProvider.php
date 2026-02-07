<?php

namespace App\Providers;

use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
