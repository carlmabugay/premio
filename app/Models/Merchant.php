<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Merchant extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'name',
        'status',
    ];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    public function apiKeys(): HasMany
    {
        return $this->hasMany(ApiKey::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function rewardRules(): HasMany
    {
        return $this->hasMany(RewardRule::class);
    }

    public function rewardIssues(): HasMany
    {
        return $this->hasMany(RewardIssue::class);
    }

    public function rewardLedgerEntries(): HasMany
    {
        return $this->hasMany(RewardLedgerEntry::class);
    }
}
