<?php

namespace App\Providers;

use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Redemptions\Contracts\RedemptionRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardLedgerEntryRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentCustomerRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRedemptionRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardIssueRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardLedgerEntryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardRuleRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->bind(EventRepositoryInterface::class, EloquentEventRepository::class);
        $this->app->bind(RewardRuleRepositoryInterface::class, EloquentRewardRuleRepository::class);
        $this->app->bind(RewardIssueRepositoryInterface::class, EloquentRewardIssueRepository::class);
        $this->app->bind(CustomerRepositoryInterface::class, EloquentCustomerRepository::class);
        $this->app->bind(RewardLedgerEntryRepositoryInterface::class, EloquentRewardLedgerEntryRepository::class);
        $this->app->bind(RedemptionRepositoryInterface::class, EloquentRedemptionRepository::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
