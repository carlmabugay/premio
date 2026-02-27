<?php

namespace App\Domain\Redemptions\Contracts;

use App\Domain\Redemptions\Entities\Redemption;

interface RedemptionRepositoryInterface
{
    public function save(Redemption $redemption): void;
}
