<?php

namespace Tests\Unit;

use App\Models\Asset;
use App\Models\KondisiFisik;
use App\Models\Pemeliharaan;
use App\Models\Efisiensi;
use App\Models\VariabelEksternal;
use App\Models\NaiveBayesDataset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ModelRelationshipTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_method_admin_mengembalikan_true_untuk_role_admin(): void
    {
        $user = User::factory()->admin()->create();

        $this->assertTrue($user->Admin());
        $this->assertFalse($user->Teknisi());
        $this->assertFalse($user->User());
    }

    public function test_user_method_teknisi_mengembalikan_true_untuk_role_teknisi(): void
    {
        $user = User::factory()->teknisi()->create();

        $this->assertFalse($user->Admin());
        $this->assertTrue($user->Teknisi());
        $this->assertFalse($user->User());
    }

    public function test_user_method_user_mengembalikan_true_untuk_role_user(): void
    {
        $user = User::factory()->user()->create();

        $this->assertFalse($user->Admin());
        $this->assertFalse($user->Teknisi());
        $this->assertTrue($user->User());
    }

    public function test_asset_has_many_kondisi_fisik(): void
    {
        $asset = Asset::factory()->create();
        KondisiFisik::factory()->count(3)->create(['id_aset' => $asset->id_aset]);

        $this->assertCount(3, $asset->kondisiFisik);
        $this->assertInstanceOf(KondisiFisik::class, $asset->kondisiFisik->first());
    }

    public function test_asset_has_many_pemeliharaan(): void
    {
        $asset = Asset::factory()->create();
        Pemeliharaan::factory()->count(2)->create(['id_aset' => $asset->id_aset]);

        $this->assertCount(2, $asset->pemeliharaan);
        $this->assertInstanceOf(Pemeliharaan::class, $asset->pemeliharaan->first());
    }

    public function test_asset_has_many_efisiensi(): void
    {
        $asset = Asset::factory()->create();
        Efisiensi::factory()->count(4)->create(['id_aset' => $asset->id_aset]);

        $this->assertCount(4, $asset->efisiensi);
        $this->assertInstanceOf(Efisiensi::class, $asset->efisiensi->first());
    }

    public function test_asset_has_many_variabel_eksternal(): void
    {
        $asset = Asset::factory()->create();
        VariabelEksternal::factory()->count(2)->create(['id_aset' => $asset->id_aset]);

        $this->assertCount(2, $asset->variabelEksternal);
        $this->assertInstanceOf(VariabelEksternal::class, $asset->variabelEksternal->first());
    }

    public function test_kondisi_fisik_belongs_to_asset(): void
    {
        $asset = Asset::factory()->create();
        $kondisi = KondisiFisik::factory()->create(['id_aset' => $asset->id_aset]);

        $this->assertInstanceOf(Asset::class, $kondisi->asset);
        $this->assertEquals($asset->id_aset, $kondisi->asset->id_aset);
    }

    public function test_pemeliharaan_belongs_to_asset(): void
    {
        $asset = Asset::factory()->create();
        $pm = Pemeliharaan::factory()->create(['id_aset' => $asset->id_aset]);

        $this->assertInstanceOf(Asset::class, $pm->asset);
        $this->assertEquals($asset->id_aset, $pm->asset->id_aset);
    }

    public function test_efisiensi_belongs_to_asset(): void
    {
        $asset = Asset::factory()->create();
        $efi = Efisiensi::factory()->create(['id_aset' => $asset->id_aset]);

        $this->assertInstanceOf(Asset::class, $efi->asset);
        $this->assertEquals($asset->id_aset, $efi->asset->id_aset);
    }

    public function test_variabel_eksternal_belongs_to_asset(): void
    {
        $asset = Asset::factory()->create();
        $var = VariabelEksternal::factory()->create(['id_aset' => $asset->id_aset]);

        $this->assertInstanceOf(Asset::class, $var->asset);
        $this->assertEquals($asset->id_aset, $var->asset->id_aset);
    }

    public function test_naive_bayes_dataset_belongs_to_asset(): void
    {
        $asset = Asset::factory()->create();
        $dataset = NaiveBayesDataset::factory()->create(['id_aset' => $asset->id_aset]);

        $this->assertInstanceOf(Asset::class, $dataset->asset);
        $this->assertEquals($asset->id_aset, $dataset->asset->id_aset);
    }

    public function test_asset_menggunakan_tabel_t_aset(): void
    {
        $asset = new Asset();
        $this->assertEquals('t_aset', $asset->getTable());
    }

    public function test_asset_menggunakan_primary_key_id_aset(): void
    {
        $asset = new Asset();
        $this->assertEquals('id_aset', $asset->getKeyName());
    }

    public function test_kondisi_fisik_cast_tgl_observasi_sebagai_date(): void
    {
        $asset = Asset::factory()->create();
        $kondisi = KondisiFisik::factory()->create([
            'id_aset' => $asset->id_aset,
            'tgl_observasi' => '2026-07-01',
        ]);

        $this->assertInstanceOf(\Illuminate\Support\Carbon::class, $kondisi->tgl_observasi);
    }

    public function test_password_user_di_hash_otomatis(): void
    {
        $user = User::factory()->create(['password' => 'password123']);

        $this->assertNotEquals('password123', $user->password);
        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('password123', $user->password));
    }
}
