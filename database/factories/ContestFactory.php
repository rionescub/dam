<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Contest;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class ContestFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected $model = Contest::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        $year = now()->year;

        $startDate = $this->faker->dateTimeBetween("May 1st {$year}", "December 31st {$year}");

        $endDate = $this->faker->dateTimeBetween($startDate, "December 31st {$year}");

        $juryDate = $this->faker->dateTimeBetween($endDate, "December 31st {$year}");

        $ceremonyDate = $this->faker->dateTimeBetween($juryDate, "December 31st {$year}");

        return [
            'name' => $this->faker->sentence(3),
            'start_date' => $startDate,
            'end_date' => $endDate,
            'jury_date' => $juryDate,
            'ceremony_date' => $ceremonyDate,
            'type' => implode(',', $this->faker->randomElements(['video', 'photo', 'craft'], rand(1, 3))),
            'phase' => $this->faker->randomElement(['local', 'phase2', 'phase3']),
            'parent_contest_id' => null,
            'rules' => $this->faker->paragraph(),
            'description' => $this->faker->paragraph(),
            'user_id' => User::first()->id,
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
}
