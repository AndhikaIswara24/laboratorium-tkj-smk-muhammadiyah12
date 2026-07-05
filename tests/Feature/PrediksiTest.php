<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Asset;
use App\Models\KondisiFisik;
use App\Models\Pemeliharaan;
use App\Models\Efisiensi;
use App\Models\VariabelEksternal;
use App\Models\NaiveBayesDataset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PrediksiTest extends TestCase
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

    public function test_admin_bisa_akses_halaman_prediksi(): void
    {
        $response = $this->actingAs($this->admin)->get('/prediksi-naive-bayes');

        $response->assertStatus(200);
        $response->assertViewIs('prediksi.index');
    }

    public function test_teknisi_tidak_bisa_akses_halaman_prediksi(): void
    {
        $response = $this->actingAs($this->teknisi)->get('/prediksi-naive-bayes');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_bisa_akses_halaman_prediksi(): void
    {
        $response = $this->actingAs($this->user)->get('/prediksi-naive-bayes');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_akses_halaman_dataset(): void
    {
        $response = $this->actingAs($this->admin)->get('/prediksi-naive-bayes/dataset');

        $response->assertStatus(200);
        $response->assertViewIs('prediksi.dataset');
        $response->assertViewHas('rows');
        $response->assertViewHas('totalDataset');
        $response->assertViewHas('totalAssets');
        $response->assertViewHas('incompleteCount');
    }

    public function test_generate_dataset_menggabungkan_data_lengkap(): void
    {
        $asset = Asset::factory()->create();
        KondisiFisik::factory()->create(['id_aset' => $asset->id_aset]);
        Pemeliharaan::factory()->create(['id_aset' => $asset->id_aset]);
        Efisiensi::factory()->create(['id_aset' => $asset->id_aset]);
        VariabelEksternal::factory()->create(['id_aset' => $asset->id_aset]);

        $response = $this->actingAs($this->admin)->post('/prediksi-naive-bayes/dataset/generate');

        $response->assertRedirect(route('prediksi.dataset'));
        $response->assertSessionHas('success');

        $this->assertDatabaseCount('t_naive_bayes_dataset', 1);
        $this->assertDatabaseHas('t_naive_bayes_dataset', ['id_aset' => $asset->id_aset]);
    }

    public function test_generate_dataset_skip_aset_data_tidak_lengkap(): void
    {
        $incompleteAsset = Asset::factory()->create();
        KondisiFisik::factory()->create(['id_aset' => $incompleteAsset->id_aset]);
        Pemeliharaan::factory()->create(['id_aset' => $incompleteAsset->id_aset]);

        $completeAsset = Asset::factory()->create();
        KondisiFisik::factory()->create(['id_aset' => $completeAsset->id_aset]);
        Pemeliharaan::factory()->create(['id_aset' => $completeAsset->id_aset]);
        Efisiensi::factory()->create(['id_aset' => $completeAsset->id_aset]);
        VariabelEksternal::factory()->create(['id_aset' => $completeAsset->id_aset]);

        $this->actingAs($this->admin)->post('/prediksi-naive-bayes/dataset/generate');

        $this->assertDatabaseCount('t_naive_bayes_dataset', 1);
        $this->assertDatabaseHas('t_naive_bayes_dataset', ['id_aset' => $completeAsset->id_aset]);
        $this->assertDatabaseMissing('t_naive_bayes_dataset', ['id_aset' => $incompleteAsset->id_aset]);
    }

    public function test_generate_dataset_membersihkan_data_lama(): void
    {
        $oldAsset = Asset::factory()->create();
        NaiveBayesDataset::factory()->create(['id_aset' => $oldAsset->id_aset]);

        $this->assertDatabaseCount('t_naive_bayes_dataset', 1);

        $this->actingAs($this->admin)->post('/prediksi-naive-bayes/dataset/generate');

        $this->assertDatabaseCount('t_naive_bayes_dataset', 0);
    }
}
