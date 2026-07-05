<?php

namespace Database\Factories;

use App\Models\Asset;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Asset>
 */
class AssetFactory extends Factory
{
    protected $model = Asset::class;

    public function definition(): array
    {
        return [
            'kode_brg' => 'AST-' . fake()->unique()->numerify('####'),
            'nama_brg' => fake()->randomElement([
                'Komputer PC', 'Laptop', 'Monitor LCD', 'Printer', 'Router',
                'Switch Hub', 'Access Point', 'Server Rack', 'UPS', 'Projector',
            ]) . ' ' . fake()->numerify('##'),
            'merk_tipe' => fake()->randomElement(['Lenovo ThinkCentre', 'HP ProDesk', 'Dell OptiPlex', 'Asus VivoPC', 'Acer Aspire']),
            'spesifikasi' => 'Intel Core i5, RAM 8GB, SSD 256GB',
            'lokasi' => fake()->randomElement(['Lab TKJ 1', 'Lab TKJ 2', 'Lab TKJ 3', 'Ruang Server', 'Ruang Guru']),
            'thn_perolehan' => (string) fake()->numberBetween(2018, 2025),
            'harga_perolehan' => fake()->randomFloat(2, 1000000, 15000000),
            'asal_usul' => fake()->randomElement(['Pembelian', 'Hibah', 'Dropping Dinas', 'Dana BOS']),
        ];
    }
}
