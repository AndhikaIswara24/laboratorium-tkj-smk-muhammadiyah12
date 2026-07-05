<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\NaiveBayesDataset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<NaiveBayesDataset>
 */
class NaiveBayesDatasetFactory extends Factory
{
    protected $model = NaiveBayesDataset::class;

    public function definition(): array
    {
        return [
            'id_aset' => Asset::factory(),
            'kondisi_brg' => fake()->randomElement(['B', 'RR', 'RB']),
            'usia_pakai' => fake()->numberBetween(0, 10),
            'frq_kerusakan' => fake()->numberBetween(0, 15),
            'jenis_pm' => fake()->randomElement(['Preventif', 'Korektif', 'Tidak Ada']),
            'interval_pm' => fake()->numberBetween(1, 12),
            'efi_out' => fake()->randomElement(['Tinggi', 'Sedang', 'Rendah']),
            'downtime' => fake()->randomFloat(1, 0, 10),
            'lingkungan' => fake()->randomElement(['Baik', 'Cukup', 'Buruk']),
            'daya_listrik' => fake()->randomElement(['Stabil', 'Tidak Stabil', 'Sering Padam']),
            'sparepart' => fake()->randomElement(['Tersedia', 'Terbatas', 'Tidak Ada']),
            'kelas_label' => fake()->randomElement(['Layak', 'Perlu Servis', 'Tidak Layak']),
            'tgl_input' => now(),
        ];
    }
}
