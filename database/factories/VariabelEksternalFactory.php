<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\VariabelEksternal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<VariabelEksternal>
 */
class VariabelEksternalFactory extends Factory
{
    protected $model = VariabelEksternal::class;

    public function definition(): array
    {
        return [
            'id_aset' => Asset::factory(),
            'tgl_observasi' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'lingkungan' => fake()->randomElement(['Baik', 'Cukup', 'Buruk']),
            'daya_listrik' => fake()->randomElement(['Stabil', 'Tidak Stabil', 'Sering Padam']),
            'sparepart' => fake()->randomElement(['Tersedia', 'Terbatas', 'Tidak Ada']),
            'anggaran' => fake()->randomElement(['Mendukung', 'Terbatas', 'Tidak Ada']),
            'ext_effect' => fake()->randomElement(['Rendah', 'Sedang', 'Tinggi']),
        ];
    }
}
