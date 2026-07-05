<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private User $teknisi;
    private User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
        $this->teknisi = User::factory()->teknisi()->create();
        $this->user = User::factory()->user()->create();
    }

    public function test_admin_bisa_melihat_daftar_aset(): void
    {
        Asset::factory()->count(3)->create();

        $response = $this->actingAs($this->admin)->get('/assets');

        $response->assertStatus(200);
        $response->assertViewIs('assets.index');
        $response->assertViewHas('items');
    }

    public function test_teknisi_bisa_melihat_daftar_aset(): void
    {
        $response = $this->actingAs($this->teknisi)->get('/assets');
        $response->assertStatus(200);
    }

    public function test_user_biasa_tidak_bisa_akses_daftar_aset(): void
    {
        $response = $this->actingAs($this->user)->get('/assets');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_melihat_form_tambah_aset(): void
    {
        $response = $this->actingAs($this->admin)->get('/assets/create');
        $response->assertStatus(200);
        $response->assertViewIs('assets.create');
    }

    public function test_admin_bisa_menambah_aset_baru(): void
    {
        $data = [
            'kode_brg' => 'AST-0001',
            'nama_brg' => 'Komputer PC Lab 1',
            'merk_tipe' => 'Lenovo ThinkCentre',
            'spesifikasi' => 'Intel i5, RAM 8GB',
            'lokasi' => 'Lab TKJ 1',
            'thn_perolehan' => '2023',
            'harga_perolehan' => 7500000,
            'asal_usul' => 'Pembelian',
        ];

        $response = $this->actingAs($this->admin)->post('/assets', $data);

        $response->assertRedirect(route('assets.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('t_aset', ['kode_brg' => 'AST-0001', 'nama_brg' => 'Komputer PC Lab 1']);
    }

    public function test_validasi_kode_barang_wajib_diisi(): void
    {
        $data = [
            'kode_brg' => '',
            'nama_brg' => 'Test',
            'asal_usul' => 'Pembelian',
        ];

        $response = $this->actingAs($this->admin)->post('/assets', $data);
        $response->assertSessionHasErrors('kode_brg');
    }

    public function test_validasi_kode_barang_harus_unik(): void
    {
        Asset::factory()->create(['kode_brg' => 'AST-UNIK']);

        $data = [
            'kode_brg' => 'AST-UNIK',
            'nama_brg' => 'Test',
            'asal_usul' => 'Pembelian',
        ];

        $response = $this->actingAs($this->admin)->post('/assets', $data);
        $response->assertSessionHasErrors('kode_brg');
    }

    public function test_validasi_asal_usul_harus_valid(): void
    {
        $data = [
            'kode_brg' => 'AST-0002',
            'nama_brg' => 'Test',
            'asal_usul' => 'Tidak Valid',
        ];

        $response = $this->actingAs($this->admin)->post('/assets', $data);
        $response->assertSessionHasErrors('asal_usul');
    }

    public function test_admin_bisa_mengupdate_aset(): void
    {
        $asset = Asset::factory()->create(['kode_brg' => 'OLD-001', 'nama_brg' => 'Lama']);

        $response = $this->actingAs($this->admin)->put("/assets/{$asset->id_aset}", [
            'kode_brg' => 'NEW-001',
            'nama_brg' => 'Baru',
            'asal_usul' => 'Hibah',
        ]);

        $response->assertRedirect(route('assets.index'));
        $this->assertDatabaseHas('t_aset', ['kode_brg' => 'NEW-001', 'nama_brg' => 'Baru']);
    }

    public function test_admin_bisa_menghapus_aset(): void
    {
        $asset = Asset::factory()->create();

        $response = $this->actingAs($this->admin)->delete("/assets/{$asset->id_aset}");

        $response->assertRedirect(route('assets.index'));
        $this->assertDatabaseMissing('t_aset', ['id_aset' => $asset->id_aset]);
    }

    public function test_fitur_search_aset_berfungsi(): void
    {
        Asset::factory()->create(['nama_brg' => 'Komputer Khusus']);
        Asset::factory()->create(['nama_brg' => 'Printer Biasa']);

        $response = $this->actingAs($this->admin)->get('/assets?search=Komputer');

        $response->assertStatus(200);
        $response->assertSee('Komputer Khusus');
    }

    public function test_filter_asal_usul_berfungsi(): void
    {
        Asset::factory()->create(['asal_usul' => 'Hibah']);
        Asset::factory()->create(['asal_usul' => 'Pembelian']);

        $response = $this->actingAs($this->admin)->get('/assets?asal_usul=Hibah');

        $response->assertStatus(200);
    }
}
