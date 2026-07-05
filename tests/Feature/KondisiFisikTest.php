<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\KondisiFisik;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KondisiFisikTest extends TestCase
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
        $this->asset = Asset::factory()->create(['thn_perolehan' => '2020']);
    }

    public function test_admin_bisa_melihat_daftar_kondisi_fisik(): void
    {
        KondisiFisik::factory()->count(3)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get('/kondisi-fisik');

        $response->assertStatus(200);
        $response->assertViewIs('kondisi-fisik.index');
        $response->assertViewHas('rows');
    }

    public function test_user_biasa_tidak_bisa_akses_kondisi_fisik(): void
    {
        $response = $this->actingAs($this->user)->get('/kondisi-fisik');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_menambah_data_kondisi_fisik(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'kondisi_brg' => 'B',
            'ket_teknis' => 'Normal',
            'frq_kerusakan' => 2,
            'kelas_label' => 'Layak',
        ];

        $response = $this->actingAs($this->admin)->post('/kondisi-fisik', $data);

        $response->assertRedirect(route('kondisi.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_kondisi_fisik', [
            'id_aset' => $this->asset->id_aset,
            'kondisi_brg' => 'B',
            'kelas_label' => 'Layak',
        ]);
    }

    public function test_usia_pakai_dihitung_otomatis_dari_tahun_perolehan(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'kondisi_brg' => 'RR',
            'ket_teknis' => 'Lemah',
            'frq_kerusakan' => 3,
            'kelas_label' => 'Perlu Servis',
        ];

        $this->actingAs($this->admin)->post('/kondisi-fisik', $data);

        $expectedUsia = (int) date('Y') - 2020;
        $this->assertDatabaseHas('t_kondisi_fisik', [
            'id_aset' => $this->asset->id_aset,
            'usia_pakai' => $expectedUsia,
        ]);
    }

    public function test_validasi_kondisi_barang_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'kondisi_brg' => 'INVALID',
            'ket_teknis' => 'Normal',
            'frq_kerusakan' => 0,
            'kelas_label' => 'Layak',
        ];

        $response = $this->actingAs($this->admin)->post('/kondisi-fisik', $data);
        $response->assertSessionHasErrors('kondisi_brg');
    }

    public function test_validasi_kelas_label_harus_valid(): void
    {
        $data = [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'kondisi_brg' => 'B',
            'ket_teknis' => 'Normal',
            'frq_kerusakan' => 0,
            'kelas_label' => 'SALAH',
        ];

        $response = $this->actingAs($this->admin)->post('/kondisi-fisik', $data);
        $response->assertSessionHasErrors('kelas_label');
    }

    public function test_admin_bisa_mengupdate_data_kondisi_fisik(): void
    {
        $kondisi = KondisiFisik::factory()->create([
            'id_aset' => $this->asset->id_aset,
            'kondisi_brg' => 'B',
        ]);

        $response = $this->actingAs($this->admin)->put("/kondisi-fisik/{$kondisi->id_kondisi}", [
            'id_aset' => $this->asset->id_aset,
            'tgl_observasi' => '2026-07-01',
            'kondisi_brg' => 'RB',
            'ket_teknis' => 'Mati Total',
            'frq_kerusakan' => 10,
            'kelas_label' => 'Tidak Layak',
        ]);

        $response->assertRedirect(route('kondisi.index'));
        $this->assertDatabaseHas('t_kondisi_fisik', [
            'id_kondisi' => $kondisi->id_kondisi,
            'kondisi_brg' => 'RB',
            'kelas_label' => 'Tidak Layak',
        ]);
    }

    public function test_admin_bisa_menghapus_data_kondisi_fisik(): void
    {
        $kondisi = KondisiFisik::factory()->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->delete("/kondisi-fisik/{$kondisi->id_kondisi}");

        $response->assertRedirect(route('kondisi.index'));
        $this->assertDatabaseMissing('t_kondisi_fisik', ['id_kondisi' => $kondisi->id_kondisi]);
    }

    public function test_bisa_melihat_history_kondisi_fisik_per_aset(): void
    {
        KondisiFisik::factory()->count(5)->create(['id_aset' => $this->asset->id_aset]);

        $response = $this->actingAs($this->admin)->get("/kondisi-fisik/history/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertViewIs('kondisi-fisik.history');
        $response->assertViewHas('asset');
        $response->assertViewHas('rows');
    }

    public function test_bisa_mengambil_data_aset_via_ajax(): void
    {
        $response = $this->actingAs($this->admin)->get("/kondisi-fisik/asset-data/{$this->asset->id_aset}");

        $response->assertStatus(200);
        $response->assertJsonStructure(['id_aset', 'kode_brg', 'nama_brg', 'thn_perolehan', 'usia_pakai']);
    }
}
