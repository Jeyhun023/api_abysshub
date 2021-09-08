<?php

namespace Database\Factories;

use App\Models\Thread;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ThreadFactory extends Factory
{
    use RefreshDatabase;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Thread::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => mt_rand(1, 15),
            'category_id' => mt_rand(1, 5),
            'title' => $this->faker->sentences(1, true),
            'slug' => $this->faker->sentences(1, true),
            'content' => $this->faker->paragraph(),
            'tags' => $this->faker->name(),
            'last_active_at' => now(),
            'answer_count' => mt_rand(1, 1500),
            'comment_count' => mt_rand(1, 99),
            'view_count' => mt_rand(1, 10000),
        ];
    }
}
