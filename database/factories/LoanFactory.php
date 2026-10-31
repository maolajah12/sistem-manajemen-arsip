<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\Archive;
use App\Models\User;

class LoanFactory extends Factory
{
    public function definition(): array
    {
        $status = $this->faker->randomElement(['pending', 'approved', 'returned', 'rejected']);

        return [
            'user_id'         => User::where('role', 'karyawan')->inRandomOrder()->first()->id ?? User::factory(),
            'archive_id'      => Archive::inRandomOrder()->first()->id ?? Archive::factory(),
            'reason'          => $this->faker->sentence(6),
            'status'          => $status,
            'return_deadline' => in_array($status, ['approved', 'returned']) ? $this->faker->dateTimeBetween('now', '+14 days') : null,
            'returned_at'     => $status === 'returned' ? now() : null,
        ];
    }
}