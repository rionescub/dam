<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contest;
use App\Models\User;
use App\Models\Work;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class WorkFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = Work::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => $this->faker->sentence(3),
            'file_path' => $this->faker->imageUrl(),
            'video_url' => $this->faker->url(),
            'description' => $this->faker->paragraph(),
            'contest_id' => Contest::factory(),
            'user_id' => User::factory(),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
}
