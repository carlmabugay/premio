<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Merchants\Contracts\MerchantRepositoryInterface;
use App\Domain\Merchants\Entities\Merchant;
use App\Models\Merchant as EloquentMerchant;

class EloquentMerchantRepository implements MerchantRepositoryInterface
{
    public function save(Merchant $merchant): void
    {
        EloquentMerchant::create([
            'name' => $merchant->name,
            'status' => $merchant->status,
        ]);
    }
}
