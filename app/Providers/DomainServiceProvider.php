<?php

namespace App\Providers;

use App\Domain\ApiKeys\Contracts\ApiKeyRepositoryInterface;
use App\Domain\Customers\Contracts\CustomerRepositoryInterface;
use App\Domain\Events\Contracts\EventRepositoryInterface;
use App\Domain\Merchants\Contracts\MerchantRepositoryInterface;
use App\Domain\Redemptions\Contracts\RedemptionRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardIssueRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardLedgerEntryRepositoryInterface;
use App\Domain\Rewards\Contracts\RewardRuleRepositoryInterface;
use App\Infrastructure\Persistence\Eloquent\EloquentApiKeyRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentCustomerRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentEventRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentMerchantRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRedemptionRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardIssueRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardLedgerEntryRepository;
use App\Infrastructure\Persistence\Eloquent\EloquentRewardRuleRepository;
use Illuminate\Support\ServiceProvider;

class DomainServiceProvider extends ServiceProvider
{
    public $bindings = [
        EventRepositoryInterface::class => EloquentEventRepository::class,
        RewardRuleRepositoryInterface::class => EloquentRewardRuleRepository::class,
        RewardIssueRepositoryInterface::class => EloquentRewardIssueRepository::class,
        CustomerRepositoryInterface::class => EloquentCustomerRepository::class,
        RewardLedgerEntryRepositoryInterface::class => EloquentRewardLedgerEntryRepository::class,
        RedemptionRepositoryInterface::class => EloquentRedemptionRepository::class,
        MerchantRepositoryInterface::class => EloquentMerchantRepository::class,
        ApiKeyRepositoryInterface::class => EloquentApiKeyRepository::class,
    ];

    /**
     * Register services.
     */
    public function register(): void {}

    /**
     * Bootstrap services.
     */
    public function boot(): void {}
}
