<?php

namespace Database\Factories;

use App\Models\Workers;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Workers>
 */
class WorkersFactory extends Factory
{
    protected $model = Workers::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'first_name' => fake()->firstName(),
            'last_name' => fake()->lastName(),
            'age' => rand(10, 50),
            'phone_number' => fake()->phoneNumber(),
        ];
    }
}
