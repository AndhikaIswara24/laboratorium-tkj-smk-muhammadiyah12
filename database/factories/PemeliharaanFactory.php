<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Pemeliharaan;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pemeliharaan>
 */
class PemeliharaanFactory extends Factory
{
    protected $model = Pemeliharaan::class;

    public function definition(): array
    {
        return [
            'id_aset' => Asset::factory(),
            'tgl_pm' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'jenis_pm' => fake()->randomElement(['Preventif', 'Korektif', 'Tidak Ada']),
            'interval_bulan' => fake()->numberBetween(1, 12),
            'pelaksana' => fake()->randomElement(['Teknisi Internal', 'Vendor Luar', 'Guru TKJ']),
            'biaya_servis' => fake()->randomFloat(2, 50000, 2000000),
            'kon_after' => fake()->randomElement(['B', 'RR', 'RB']),
            'ket_pm' => fake()->optional()->sentence(),
        ];
    }
}
