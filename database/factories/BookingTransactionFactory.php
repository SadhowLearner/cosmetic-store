<?php

namespace Database\Factories;

use App\Models\BookingTransaction;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<BookingTransaction>
 */
class BookingTransactionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_trx_id' => 'SVX-'.Str::upper(Str::random(10)),
            'name' => fake()->name(),
            'email' => fake()->email(),
            'phone' => fake()->phoneNumber(),
            'proof' => null,
            'post_code' => fake()->postcode(),
            'city' => fake()->city(),
            'address' => fake()->address(),
            'sub_total_amount' => fake()->numberBetween(100000, 500000),
            'total_amount' => fake()->numberBetween(110000, 600000),
            'total_tax_amount' => fake()->numberBetween(10000, 55000),
            'total_qty' => fake()->numberBetween(1, 10),
            'is_paid' => fake()->boolean(),
        ];
    }

    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => true,
        ]);
    }

    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_paid' => false,
        ]);
    }
}
