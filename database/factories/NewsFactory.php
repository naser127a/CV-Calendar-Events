<?php

namespace Database\Factories;

use App\Models\News;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<News>
 */
class NewsFactory extends Factory
{
    protected $model = News::class;

    public function definition(): array
    {
        $isBreaking = fake()->boolean();

        return [
            'title' => fake()->sentence(6),

            'summary' => fake()->sentence(15),

            'content' => fake()->paragraphs(3, true),

            'image' => null,

            'type' => fake()->randomElement([
                'news',
                'announcement',
            ]),

            'source_type' => 'local',

            'external_id' => null,

            'external_url' => null,

            'published_at' => fake()->dateTime(),

            'status' => true,

            'is_breaking' => $isBreaking,

            'breaking_until' => $isBreaking
                ? fake()->dateTimeBetween('now', '+7 days')
                : null,

            'created_by' => User::factory(),
        ];
    }
}
