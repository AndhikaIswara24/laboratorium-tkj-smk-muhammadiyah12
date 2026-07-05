<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\VariabelEksternal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VariabelEksternalTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teknisi;
    private User $user;
    private Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->teknisi = User::factory()->teknisi()->create();
        $this->user = User::factory()->user()->create();
        $this->asset = Asset::factory()->create();
    }

    public function test_admin_bisa_melihat_daftar_variabel_eksternal(): void
    {
        VariabelEksternal::factory()->count(3)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get('/variabel-eksternal');

        $response->assertStatus(200);
        $response->assertViewIs('variabel-eksternal.index');
        $response->assertViewHas('rows');
    }

    public function test_teknisi_tidak_bisa_akses_variabel_eksternal(): void
    {
        $response = $this->actingAs($this->teknisi)->get('/variabel-eksternal');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_bisa_akses_variabel_eksternal(): void
    {
        $response = $this->actingAs($this->user)->get('/variabel-eksternal');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_data_variabel_eksternal(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'lingkungan' => 'Baik',
            'daya_listrik' => 'Stabil',
            'sparepart' => 'Tersedia',
            'anggaran' => 'Mendukung',
            'ext_effect' => 'Rendah',
        ];

        $response = $this->actingAs($this->admin)->post('/variabel-eksternal', $data);

        $response->assertRedirect(route('variabel.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_variabel_eksternal', [
            'id_aset' => $this->asset->id_aset,
            'lingkungan' => 'Baik',
            'ext_effect' => 'Rendah',
        ]);
    }

    public function test_validasi_lingkungan_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'lingkungan' => 'Sangat Baik',
            'daya_listrik' => 'Stabil',
            'sparepart' => 'Tersedia',
            'anggaran' => 'Mendukung',
            'ext_effect' => 'Rendah',
        ];

        $response = $this->actingAs($this->admin)->post('/variabel-eksternal', $data);
        $response->assertSessionHasErrors('lingkungan');
    }

    public function test_validasi_daya_listrik_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'lingkungan' => 'Baik',
            'daya_listrik' => 'Sangat Stabil',
            'sparepart' => 'Tersedia',
            'anggaran' => 'Mendukung',
            'ext_effect' => 'Rendah',
        ];

        $response = $this->actingAs($this->admin)->post('/variabel-eksternal', $data);
        $response->assertSessionHasErrors('daya_listrik');
    }

    public function test_validasi_semua_field_wajib(): void
    {
        $response = $this->actingAs($this->admin)->post('/variabel-eksternal', []);

        $response->assertSessionHasErrors(['id_aset', 'tgl_observasi', 'lingkungan', 'daya_listrik', 'sparepart', 'anggaran', 'ext_effect']);
    }

    public function test_admin_bisa_mengupdate_data_variabel_eksternal(): void
    {
        $variabel = VariabelEksternal::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->put("/variabel-eksternal/{$variabel->id_eksternal}", [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'lingkungan' => 'Buruk',
            'daya_listrik' => 'Sering Padam',
            'sparepart' => 'Tidak Ada',
            'anggaran' => 'Tidak Ada',
            'ext_effect' => 'Tinggi',
        ]);

        $response->assertRedirect(route('variabel.index'));
        $this->assertDatabaseHas('t_variabel_eksternal', [
            'id_eksternal' => $variabel->id_eksternal,
            'lingkungan' => 'Buruk',
            'ext_effect' => 'Tinggi',
        ]);
    }

    public function test_admin_bisa_menghapus_data_variabel_eksternal(): void
    {
        $variabel = VariabelEksternal::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->delete("/variabel-eksternal/{$variabel->id_eksternal}");

        $response->assertRedirect(route('variabel.index'));
        $this->assertDatabaseMissing('t_variabel_eksternal', ['id_eksternal' => $variabel->id_eksternal]);
    }

    public function test_bisa_melihat_history_variabel_eksternal_per_aset(): void
    {
        VariabelEksternal::factory()->count(3)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get("/variabel-eksternal/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewIs('variabel-eksternal.history');
        $response->assertViewHas('asset');
        $response->assertViewHas('rows');
    }
}
