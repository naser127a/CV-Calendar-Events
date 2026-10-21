<?php

namespace Database\Factories;

use App\Models\CalendarEvent;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<CalendarEvent>
 */
class CalendarEventFactory extends Factory
{
    protected $model = CalendarEvent::class;

    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->sentence(),
            'start_at' => fake()->dateTimeBetween('+1 day', '+1 month'),
            'end_at' => fake()->optional()->dateTimeBetween('+1 month', '+2 months'),
            'all_day' => false,
            'color' => fake()->safeHexColor(),
            'created_by' => User::factory(),
            'status' => true,
        ];
    }
}
