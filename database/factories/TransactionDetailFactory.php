<?php

namespace Database\Factories;

use App\Models\BookingTransaction;
use App\Models\Cosmetic;
use App\Models\TransactionDetail;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TransactionDetail>
 */
class TransactionDetailFactory extends Factory
{
    public function definition(): array
    {
        return [
            'booking_transaction_id' => BookingTransaction::factory(),
            'cosmetic_id' => Cosmetic::factory(),
            'price' => fake()->numberBetween(50000, 500000),
            'qty' => fake()->numberBetween(1, 5),
        ];
    }
}
