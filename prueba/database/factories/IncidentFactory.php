<?php

namespace Database\Factories;

use App\Models\Incident;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;

/**
 * @extends Factory<Incident>
 */
class IncidentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => $this->faker->randomElement(['pending', 'in_progress', 'resolved']),
            'priority' => $this->faker->randomElement(['low', 'medium', 'high']),
            'created_user_id' => User::inRandomOrder()->value('id'),
            'assigned_user_id' => User::inRandomOrder()->value('id'),
            'expiration_date' => $this->faker->dateTimeBetween('+1 day', '+1 month'),
        ];
    }
}
