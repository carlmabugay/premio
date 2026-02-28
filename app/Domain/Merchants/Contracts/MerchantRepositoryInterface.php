<?php

namespace App\Domain\Merchants\Contracts;

use App\Domain\Merchants\Entities\Merchant;

interface MerchantRepositoryInterface
{
    public function save(Merchant $merchant);
}
