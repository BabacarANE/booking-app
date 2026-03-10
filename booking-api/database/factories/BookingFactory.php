<?php

namespace Database\Factories;

use App\Models\Resource;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BookingFactory extends Factory
{
    public function definition(): array
    {
        $checkIn  = $this->faker->dateTimeBetween('+1 days', '+30 days');
        $checkOut = $this->faker->dateTimeBetween($checkIn, '+60 days');

        return [
            'user_id'          => User::factory(),
            'resource_id'      => Resource::factory(),
            'check_in_date'    => $checkIn->format('Y-m-d'),
            'check_out_date'   => $checkOut->format('Y-m-d'),
            'total_price'      => $this->faker->randomFloat(2, 100, 2000),
            'status'           => 'pending',
            'payment_status'   => 'pending',
            'payment_intent_id'=> null,
            'special_requests' => null,
        ];
    }
}
