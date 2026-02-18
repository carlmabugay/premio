<?php

namespace Database\Factories;

use App\Models\Event;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Event>
 */
class EventFactory extends Factory
{
    protected $model = Event::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'external_id' => 'EXT-'.$this->faker->unique()->numerify('#####'),
            'type' => 'order.completed',
            'source' => $this->faker->randomElement(['shopify', 'stripe']),
            'payload' => [
                'amount' => $this->faker->numberBetween(100, 5000),
            ],
            'occurred_at' => now(),

        ];
    }

    public function sameIdentity(string $externalId, string $source): EventFactory|Factory
    {
        return $this->state(fn () => [
            'external_id' => $externalId,
            'source' => $source,
        ]);
    }

    public function sameExternalIdDifferentSource(string $externalId): EventFactory|Factory
    {
        return $this->state(fn () => [
            'external_id' => $externalId,
            'source' => 'stripe',
        ]);
    }
}
