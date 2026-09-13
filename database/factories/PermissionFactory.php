<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class PermissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(2),
            'display_name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
