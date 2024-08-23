<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contest;
use App\Models\User;
use App\Models\Work;
use App\Models\Score;
use App\Models\Diploma;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<User>
 */
class DiplomaFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = Diploma::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'contest_id' => Contest::factory(),
            'work_id' => Work::factory(),
            'score_id' => Score::factory(),
            'description' => $this->faker->paragraph(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
}
