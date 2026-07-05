<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\KondisiFisik;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<KondisiFisik>
 */
class KondisiFisikFactory extends Factory
{
    protected $model = KondisiFisik::class;

    public function definition(): array
    {
        return [
            'id_aset' => Asset::factory(),
            'tgl_observasi' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'kondisi_brg' => fake()->randomElement(['B', 'RR', 'RB']),
            'ket_teknis' => fake()->randomElement(['Normal', 'Lemah', 'Lambat', 'Mati Total']),
            'usia_pakai' => fake()->numberBetween(0, 10),
            'frq_kerusakan' => fake()->numberBetween(0, 15),
            'kelas_label' => fake()->randomElement(['Layak', 'Perlu Servis', 'Tidak Layak']),
        ];
    }
}
