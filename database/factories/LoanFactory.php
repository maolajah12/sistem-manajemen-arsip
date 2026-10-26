<?php

namespace Database\Factories;

use App\Models\Loan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Loan>
 */
class LoanFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    $isReturned = fake()->boolean(70); // 70% peluang dokumen sudah dikembalikan

    return [
        'archive_id' => fake()->numberBetween(1, 100), // Mengambil acak dari 100 arsip
        'borrower_name' => fake()->name(),
        'division' => fake()->randomElement(['Keuangan', 'SDM', 'Legal', 'Operasional', 'IT']),
        'loan_date' => fake()->dateTimeBetween('-1 month', '-1 week'),
        'return_date' => $isReturned ? fake()->dateTimeBetween('-6 days', 'now') : null,
        'status' => $isReturned ? 'Dikembalikan' : 'Dipinjam',
    ];
}
}
