<?php

namespace Database\Factories;

use App\Models\Answer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AnswerFactory extends Factory
{
    use RefreshDatabase;
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Answer::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'thread_id' => mt_rand(1, 15),
            'user_id' => mt_rand(1, 15),
            'parent_id' => null,
            'content' => $this->faker->paragraph(),
        ];
    }

}
