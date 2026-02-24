<?php

namespace Database\Factories;

use App\Models\ApiKey;
use App\Models\Merchant;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ApiKey>
 */
class ApiKeyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->name(),
            'merchant_id' => Merchant::factory(),
            'key_hash' => $this->faker->sha256(),
            'is_active' => true,
        ];
    }
}
