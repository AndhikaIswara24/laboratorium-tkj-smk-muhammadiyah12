<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\KondisiFisik;
use App\Models\Pemeliharaan;
use App\Models\Efisiensi;
use App\Models\VariabelEksternal;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_tidak_bisa_akses_dashboard(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_admin_melihat_dashboard_admin(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.admin');
    }

    public function test_teknisi_melihat_dashboard_teknisi(): void
    {
        $teknisi = User::factory()->teknisi()->create();

        $response = $this->actingAs($teknisi)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.teknisi');
    }

    public function test_user_biasa_melihat_dashboard_user(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewIs('dashboard.user');
    }

    public function test_dashboard_menampilkan_statistik_yang_benar(): void
    {
        $admin = User::factory()->admin()->create();

        $asset = Asset::factory()->create();
        KondisiFisik::factory()->create(['id_aset' => $asset->id_aset, 'kelas_label' => 'Layak']);
        KondisiFisik::factory()->create(['id_aset' => $asset->id_aset, 'kelas_label' => 'Perlu Servis']);
        Pemeliharaan::factory()->create(['id_aset' => $asset->id_aset]);
        Efisiensi::factory()->create(['id_aset' => $asset->id_aset]);
        VariabelEksternal::factory()->create(['id_aset' => $asset->id_aset]);

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('totalAssets', 1);
        $response->assertViewHas('countKondisi', 2);
        $response->assertViewHas('countPemeliharaan', 1);
        $response->assertViewHas('countEfisiensi', 1);
        $response->assertViewHas('countVariabel', 1);
    }

    public function test_dashboard_mendeteksi_aset_dengan_data_tidak_lengkap(): void
    {
        $admin = User::factory()->admin()->create();
        Asset::factory()->create();

        $response = $this->actingAs($admin)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertViewHas('incompleteAssets');

        $incompleteAssets = $response->viewData('incompleteAssets');
        $this->assertCount(1, $incompleteAssets);
        $this->assertCount(4, $incompleteAssets[0]['missing']);
    }
}
