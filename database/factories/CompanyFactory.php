<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Company>
 */
class CompanyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'category_id' => Category::factory()->create(),
            'uuid' => $this->faker->unique()->uuid(),
            'url' => $this->faker->slug,
            'name' => $this->faker->unique()->name(),
            'email' => $this->faker->unique()->email(),
            'whatsapp' => $this->faker->unique()->numberBetween(10000000000, 99999999999)
        ];
    }
}
