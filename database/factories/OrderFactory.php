<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'public_id' => strtolower((string) Str::ulid()),
            'subtotal' => $this->faker->randomFloat(2, 0, 999999),
            'tax' => $this->faker->randomFloat(2, 0, 999999),
            'shipping' => $this->faker->randomFloat(2, 0, 999999),
            'total' => $this->faker->randomFloat(2, 0, 999999),
            'payment_method' => $this->faker->word,
            'payment_gateway' => $this->faker->word,
            'payment_id' => $this->faker->word,
        ];
    }
}
