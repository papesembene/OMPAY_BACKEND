<?php

namespace Database\Factories;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class MarchantFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'id' => (string) Str::uuid(),
            'code' => 'MCH' . $this->faker->unique()->numberBetween(100000, 999999),
            'name' => $this->faker->company() . ' Boutique',
            'phone' => '+221' . $this->faker->numerify('7########'),
        ];
    }
}
