<?php


namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class RoleFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->slug(),
            'display_name' => fake()->words(2, true),
            'description' => fake()->sentence(),
            'is_active' => true,
        ];
    }
}
// namespace Database\Factories;

// use Illuminate\Database\Eloquent\Factories\Factory;

// class RoleFactory extends Factory
// {
//     public function definition(): array
//     {
//         return [
//             'name' => fake()->unique()->slug(2),
//             'display_name' => fake()->words(2, true),
//             'description' => fake()->sentence(),
//         ];
//     }

//     public function admin(): static
//     {
//         return $this->state([
//             'name' => 'admin',
//             'display_name' => 'المدير العام',
//             'description' => 'يمتلك كافة الصلاحيات',
//         ]);
//     }

//     public function supervisor(): static
//     {
//         return $this->state([
//             'name' => 'supervisor',
//             'display_name' => 'مشرف',
//         ]);
//     }

//     public function user(): static
//     {
//         return $this->state([
//             'name' => 'user',
//             'display_name' => 'مستخدم عادي',
//         ]);
//     }
// }
