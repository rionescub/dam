<?php

namespace Database\Factories;

use App\Models\Score;
use App\Models\User;
use App\Models\Work;
use Illuminate\Database\Eloquent\Factories\Factory;

class ScoreFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Score::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $creativity_score = $this->faker->numberBetween(0, 10);
        $link_score = $this->faker->numberBetween(0, 10);
        $aesthetic_score = $this->faker->numberBetween(0, 10);
        $total_score = $creativity_score + $link_score + $aesthetic_score;

        return [
            'user_id' => User::factory(),
            'work_id' => Work::factory(),
            'creativity_score' => $creativity_score,
            'link_score' => $link_score,
            'aesthetic_score' => $aesthetic_score,
            'total_score' => $total_score
        ];
    }
}
