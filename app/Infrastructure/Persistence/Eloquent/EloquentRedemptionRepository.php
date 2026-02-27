<?php

namespace App\Infrastructure\Persistence\Eloquent;

use App\Domain\Redemptions\Contracts\RedemptionRepositoryInterface;
use App\Domain\Redemptions\Entities\Redemption;
use App\Models\Redemption as EloquentRedemption;

class EloquentRedemptionRepository implements RedemptionRepositoryInterface
{
    public function save(Redemption $redemption): void
    {
        EloquentRedemption::create([
            'merchant_id' => $redemption->merchantId(),
            'customer_id' => $redemption->customerId(),
            'type' => $redemption->type(),
            'external_id' => $redemption->externalId(),
            'points' => $redemption->points(),
            'status' => $redemption->status(),
            'meta_data' => $redemption->metaData(),
            'processed_at' => $redemption->processedAt(),
            'created_at' => $redemption->createdAt(),
        ]);
    }
}
