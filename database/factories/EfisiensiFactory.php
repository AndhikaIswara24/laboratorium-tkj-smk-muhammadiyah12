<?php

namespace Database\Factories;

use App\Models\Asset;
use App\Models\Efisiensi;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Efisiensi>
 */
class EfisiensiFactory extends Factory
{
    protected $model = Efisiensi::class;

    public function definition(): array
    {
        return [
            'id_aset' => Asset::factory(),
            'tgl_observasi' => fake()->dateTimeBetween('-1 year', 'now')->format('Y-m-d'),
            'jam_ops' => fake()->randomFloat(1, 0, 24),
            'penggunaan' => fake()->randomElement(['Tinggi', 'Sedang', 'Tidak Pakai']),
            'jml_user' => fake()->numberBetween(0, 40),
            'downtime' => fake()->randomFloat(1, 0, 10),
            'perform' => fake()->randomElement(['Normal', 'Lambat', 'Mati']),
            'umur_ekonomis' => fake()->numberBetween(1, 10),
            'efi_out' => fake()->randomElement(['Tinggi', 'Sedang', 'Rendah']),
        ];
    }
}
