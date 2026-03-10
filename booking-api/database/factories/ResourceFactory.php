<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class ResourceFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(3, true);
        return [
            'category_id'     => Category::factory(),
            'name'            => $name,
            'slug'            => Str::slug($name) . '-' . uniqid(),
            'description'     => $this->faker->paragraph(),
            'price_per_night' => $this->faker->randomFloat(2, 50, 500),
            'capacity'        => $this->faker->numberBetween(1, 10),
            'amenities'       => ['WiFi', 'TV', 'Climatisation'],
            'images'          => [],
            'location'        => $this->faker->city(),
            'is_active'       => true,
        ];
    }
}
