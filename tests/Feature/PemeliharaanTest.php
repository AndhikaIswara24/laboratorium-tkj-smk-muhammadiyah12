<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\Pemeliharaan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PemeliharaanTest extends TestCase
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

    public function test_admin_bisa_melihat_daftar_pemeliharaan(): void
    {
        Pemeliharaan::factory()->count(3)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get('/pemeliharaan');

        $response->assertStatus(200);
        $response->assertViewIs('pemeliharaan.index');
        $response->assertViewHas('rows');
    }

    public function test_user_biasa_tidak_bisa_akses_pemeliharaan(): void
    {
        $response = $this->actingAs($this->user)->get('/pemeliharaan');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_data_pemeliharaan(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-01',
            'jenis_pm' => 'Preventif',
            'interval_bulan' => 6,
            'pelaksana' => 'Teknisi Internal',
            'biaya_servis' => 500000,
            'kon_after' => 'B',
            'ket_pm' => 'Cleaning dan pengecekan rutin.',
        ];

        $response = $this->actingAs($this->admin)->post('/pemeliharaan', $data);

        $response->assertRedirect(route('pemeliharaan.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_pemeliharaan', [
            'id_aset' => $this->asset->id_aset,
            'jenis_pm' => 'Preventif',
            'pelaksana' => 'Teknisi Internal',
        ]);
    }

    public function test_validasi_jenis_pm_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-01',
            'jenis_pm' => 'Tidak Terdaftar',
            'interval_bulan' => 3,
            'pelaksana' => 'Teknisi Internal',
            'biaya_servis' => 100000,
            'kon_after' => 'B',
        ];

        $response = $this->actingAs($this->admin)->post('/pemeliharaan', $data);
        $response->assertSessionHasErrors('jenis_pm');
    }

    public function test_validasi_pelaksana_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-01',
            'jenis_pm' => 'Korektif',
            'interval_bulan' => 3,
            'pelaksana' => 'Orang Random',
            'biaya_servis' => 100000,
            'kon_after' => 'B',
        ];

        $response = $this->actingAs($this->admin)->post('/pemeliharaan', $data);
        $response->assertSessionHasErrors('pelaksana');
    }

    public function test_validasi_biaya_servis_tidak_boleh_negatif(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-01',
            'jenis_pm' => 'Preventif',
            'interval_bulan' => 3,
            'pelaksana' => 'Teknisi Internal',
            'biaya_servis' => -50000,
            'kon_after' => 'B',
        ];

        $response = $this->actingAs($this->admin)->post('/pemeliharaan', $data);
        $response->assertSessionHasErrors('biaya_servis');
    }

    public function test_admin_bisa_mengupdate_data_pemeliharaan(): void
    {
        $pm = Pemeliharaan::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->put("/pemeliharaan/{$pm->id_pm}", [
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-01',
            'jenis_pm' => 'Korektif',
            'interval_bulan' => 1,
            'pelaksana' => 'Vendor Luar',
            'biaya_servis' => 1500000,
            'kon_after' => 'RR',
            'ket_pm' => 'Ganti komponen rusak.',
        ]);

        $response->assertRedirect(route('pemeliharaan.index'));
        $this->assertDatabaseHas('t_pemeliharaan', [
            'id_pm' => $pm->id_pm,
            'jenis_pm' => 'Korektif',
            'pelaksana' => 'Vendor Luar',
        ]);
    }

    public function test_admin_bisa_menghapus_data_pemeliharaan(): void
    {
        $pm = Pemeliharaan::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->delete("/pemeliharaan/{$pm->id_pm}");

        $response->assertRedirect(route('pemeliharaan.index'));
        $this->assertDatabaseMissing('t_pemeliharaan', ['id_pm' => $pm->id_pm]);
    }

    public function test_bisa_melihat_history_pemeliharaan_per_aset(): void
    {
        // Recent records (within 24h) should be visible
        Pemeliharaan::factory()->count(3)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now(),
        ]);

        $response = $this->actingAs($this->admin)->get("/pemeliharaan/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewIs('pemeliharaan.history');
        $response->assertViewHas('asset');
        $response->assertViewHas('rows', function ($rows) {
            return $rows->count() === 3;
        });
    }

    public function test_history_pemeliharaan_hides_expired_records(): void
    {
        // Create 2 recent records (within 24h)
        Pemeliharaan::factory()->count(2)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now(),
        ]);

        // Create 3 expired records (older than 24h)
        Pemeliharaan::factory()->count(3)->create([
            'id_aset' => $this->asset->id_aset,
            'created_at' => now()->subHours(25),
        ]);

        $response = $this->actingAs($this->admin)->get("/pemeliharaan/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewHas('rows', function ($rows) {
            return $rows->count() === 2;
        });

        // Verify expired records still exist in the database (not deleted)
        $this->assertDatabaseCount('t_pemeliharaan', 5);
    }

    public function test_laporan_pemeliharaan_menampilkan_keterangan_dan_urutan_awal_ke_terbaru(): void
    {
        Pemeliharaan::factory()->create([
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-07-10',
            'ket_pm' => 'Pengecekan akhir tahunan.',
        ]);

        Pemeliharaan::factory()->create([
            'id_aset' => $this->asset->id_aset,
            'tgl_pm' => '2026-04-05',
            'ket_pm' => 'Perbaikan awal bulan april.',
        ]);

        $response = $this->actingAs($this->admin)->get('/laporan/generate?tipe=pemeliharaan&action=print');

        $response->assertStatus(200);
        $response->assertSee('Keterangan');
        $response->assertSeeInOrder(['05-04-2026', '10-07-2026']);
    }
}

