<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TodoFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'task' => $this->faker->sentence(),
            'due_date' => $this->faker->optional()->dateTimeThisMonth(),
            'completed' => $this->faker->boolean()
        ];
    }
}
