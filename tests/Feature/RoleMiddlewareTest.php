<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RoleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_diredirect_ke_login_untuk_route_terproteksi(): void
    {
        $response = $this->get('/assets');
        $response->assertRedirect('/login');
    }

    public function test_admin_bisa_akses_route_admin_only(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/variabel-eksternal');
        $response->assertStatus(200);
    }

    public function test_teknisi_tidak_bisa_akses_route_admin_only(): void
    {
        $teknisi = User::factory()->teknisi()->create();

        $response = $this->actingAs($teknisi)->get('/variabel-eksternal');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_bisa_akses_route_admin_teknisi(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/assets');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_akses_route_admin_teknisi(): void
    {
        $admin = User::factory()->admin()->create();

        $response = $this->actingAs($admin)->get('/assets');
        $response->assertStatus(200);
    }

    public function test_teknisi_bisa_akses_route_admin_teknisi(): void
    {
        $teknisi = User::factory()->teknisi()->create();

        $response = $this->actingAs($teknisi)->get('/assets');
        $response->assertStatus(200);
    }

    public function test_user_biasa_tidak_bisa_akses_prediksi(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/prediksi-naive-bayes');
        $response->assertStatus(403);
    }

    public function test_teknisi_tidak_bisa_akses_prediksi(): void
    {
        $teknisi = User::factory()->teknisi()->create();

        $response = $this->actingAs($teknisi)->get('/prediksi-naive-bayes');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_bisa_akses_admin_users(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);
    }
}
