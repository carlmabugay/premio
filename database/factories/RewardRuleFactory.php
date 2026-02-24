<?php

namespace Database\Factories;

use App\Models\Merchant;
use App\Models\RewardRule;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RewardRule>
 */
class RewardRuleFactory extends Factory
{
    protected $model = RewardRule::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'merchant_id' => Merchant::factory(),
            'name' => 'Reward Rule '.$this->faker->word(),
            'event_type' => 'order.completed',
            'is_active' => true,
            'starts_at' => null,
            'ends_at' => null,
            'conditions' => json_encode([
                [
                    'field' => 'amount',
                    'operator' => '>=',
                    'value' => 100,
                ],
            ]),
            'priority' => $this->faker->numberBetween(1, 100),
        ];
    }

    public function inactive(): RewardRuleFactory|Factory
    {
        return $this->state(fn () => [
            'is_active' => false,
        ]);
    }

    public function forEvent(string $eventType): RewardRuleFactory|Factory
    {
        return $this->state(fn () => [
            'event_type' => $eventType,
        ]);
    }

    public function withPriority(int $priority): RewardRuleFactory|Factory
    {
        return $this->state(fn () => [
            'priority' => $priority,
        ]);
    }

    public function withDateWindow(?string $start, ?string $end): RewardRuleFactory|Factory
    {
        return $this->state(fn () => [
            'starts_at' => $start,
            'ends_at' => $end,
        ]);
    }

    public function withoutConditions(): RewardRuleFactory|Factory
    {
        return $this->state(fn () => [
            'conditions' => [],
        ]);
    }
}
