<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
 */
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Incident>
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
            'reference_number' => 'INC-' . date('Ymd') . '-' . str_pad($this->faker->unique()->numberBetween(1, 9999), 4, '0', STR_PAD_LEFT),
            'company_id' => \App\Models\Company::factory(),
            'reported_by' => \App\Models\User::factory(),
            'reporter_name' => fake()->name(),
            'reporter_email' => fake()->email(),
            'reporter_phone' => fake()->phoneNumber(),
            'incident_type' => fake()->randomElement(['Safety', 'Health', 'Environment', 'Security']),
            'title' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'location' => fake()->address(),
            'incident_date' => fake()->dateTimeBetween('-30 days', 'now'),
            'severity' => fake()->randomElement(['low', 'medium', 'high', 'critical']),
            'status' => fake()->randomElement(['reported', 'open', 'investigating', 'resolved', 'closed']),
            'actions_taken' => fake()->optional()->paragraph(),
            'images' => null,
        ];
    }
}

