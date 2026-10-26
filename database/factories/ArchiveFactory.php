<?php

namespace Database\Factories;

use App\Models\Archive;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Archive>
 */
class ArchiveFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
{
    return [
        'document_number' => 'DOC-' . fake()->unique()->numerify('####-####'),
        'title' => fake()->sentence(4),
        'category_id' => fake()->numberBetween(1, 5), // Menautkan ke 5 kategori statis
        'location_id' => fake()->numberBetween(1, 5), // Menautkan ke 5 lokasi statis
        'file_path' => null,
        'retention_date' => fake()->dateTimeBetween('now', '+5 years'),
        'status' => fake()->randomElement(['Tersedia', 'Dipinjam']),
    ];
}
}
