<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\Efisiensi;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EfisiensiTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $user;
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->user = User::factory()->user()->create();
        $this->asset = Asset::factory()->create();
    }

    public function test_admin_bisa_melihat_daftar_efisiensi(): void
    {
        Efisiensi::factory()->count(3)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get('/efisiensi');

        $response->assertStatus(200);
        $response->assertViewIs('efisiensi.index');
        $response->assertViewHas('rows');
    }

    public function test_user_biasa_tidak_bisa_akses_efisiensi(): void
    {
        $response = $this->actingAs($this->user)->get('/efisiensi');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_data_efisiensi(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'jam_ops' => 8.5,
            'penggunaan' => 'Tinggi',
            'jml_user' => 30,
            'downtime' => 0.5,
            'perform' => 'Normal',
            'umur_ekonomis' => 5,
            'efi_out' => 'Tinggi',
        ];

        $response = $this->actingAs($this->admin)->post('/efisiensi', $data);

        $response->assertRedirect(route('efisiensi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_efisiensi', [
            'id_aset' => $this->asset->id_aset,
            'penggunaan' => 'Tinggi',
            'efi_out' => 'Tinggi',
        ]);
    }

    public function test_validasi_penggunaan_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'jam_ops' => 8,
            'penggunaan' => 'INVALID',
            'jml_user' => 10,
            'downtime' => 1,
            'perform' => 'Normal',
            'umur_ekonomis' => 5,
            'efi_out' => 'Tinggi',
        ];

        $response = $this->actingAs($this->admin)->post('/efisiensi', $data);
        $response->assertSessionHasErrors('penggunaan');
    }

    public function test_validasi_perform_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'jam_ops' => 8,
            'penggunaan' => 'Tinggi',
            'jml_user' => 10,
            'downtime' => 1,
            'perform' => 'Super Cepat',
            'umur_ekonomis' => 5,
            'efi_out' => 'Tinggi',
        ];

        $response = $this->actingAs($this->admin)->post('/efisiensi', $data);
        $response->assertSessionHasErrors('perform');
    }

    public function test_validasi_downtime_tidak_boleh_negatif(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'jam_ops' => 8,
            'penggunaan' => 'Tinggi',
            'jml_user' => 10,
            'downtime' => -5,
            'perform' => 'Normal',
            'umur_ekonomis' => 5,
            'efi_out' => 'Tinggi',
        ];

        $response = $this->actingAs($this->admin)->post('/efisiensi', $data);
        $response->assertSessionHasErrors('downtime');
    }

    public function test_admin_bisa_mengupdate_data_efisiensi(): void
    {
        $efisiensi = Efisiensi::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->put("/efisiensi/{$efisiensi->id_efisiensi}", [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'jam_ops' => 4,
            'penggunaan' => 'Sedang',
            'jml_user' => 15,
            'downtime' => 2,
            'perform' => 'Lambat',
            'umur_ekonomis' => 3,
            'efi_out' => 'Sedang',
        ]);

        $response->assertRedirect(route('efisiensi.index'));
        $this->assertDatabaseHas('t_efisiensi', [
            'id_efisiensi' => $efisiensi->id_efisiensi,
            'penggunaan' => 'Sedang',
            'efi_out' => 'Sedang',
        ]);
    }

    public function test_admin_bisa_menghapus_data_efisiensi(): void
    {
        $efisiensi = Efisiensi::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->delete("/efisiensi/{$efisiensi->id_efisiensi}");

        $response->assertRedirect(route('efisiensi.index'));
        $this->assertDatabaseMissing('t_efisiensi', ['id_efisiensi' => $efisiensi->id_efisiensi]);
    }

    public function test_bisa_melihat_history_efisiensi_per_aset(): void
    {
        // Recent records (within 24h) should be visible
        Efisiensi::factory()->count(3)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get("/efisiensi/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewIs('efisiensi.history');
        $response->assertViewHas('asset');
        $response->assertViewHas('rows', function ($rows) {
            return $rows->count() === 3;
        });
    }

    public function test_history_efisiensi_hides_expired_records(): void
    {
        // Create 2 recent records (within 24h)
        Efisiensi::factory()->count(2)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now(),
        ]);

        // Create 3 expired records (older than 24h)
        Efisiensi::factory()->count(3)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now()->subHours(25),
        ]);

        $response = $this->actingAs($this->admin)->get("/efisiensi/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewHas('rows', function ($rows) {
            return $rows->count() === 2;
        });

        // Verify expired records still exist in the database (not deleted)
        $this->assertDatabaseCount('t_efisiensi', 5);
    }
}

