<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_bisa_melihat_daftar_user(): void
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->admin)->get('/admin/users');

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.index');
        $response->assertViewHas('users');
    }

    public function test_teknisi_tidak_bisa_akses_user_management(): void
    {
        $teknisi = User::factory()->teknisi()->create();

        $response = $this->actingAs($teknisi)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_user_biasa_tidak_bisa_akses_user_management(): void
    {
        $user = User::factory()->user()->create();

        $response = $this->actingAs($user)->get('/admin/users');
        $response->assertStatus(403);
    }

    public function test_admin_bisa_melihat_form_edit_user(): void
    {
        $targetUser = User::factory()->user()->create();

        $response = $this->actingAs($this->admin)->get("/admin/users/{$targetUser->id}/edit");

        $response->assertStatus(200);
        $response->assertViewIs('admin.users.edit');
    }

    public function test_admin_bisa_mengubah_role_user(): void
    {
        $targetUser = User::factory()->user()->create();

        $response = $this->actingAs($this->admin)->patch("/admin/users/{$targetUser->id}", [
            'role' => 'teknisi',
        ]);

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseHas('users', [
            'id' => $targetUser->id,
            'role' => 'teknisi',
        ]);
    }

    public function test_validasi_role_harus_valid(): void
    {
        $targetUser = User::factory()->user()->create();

        $response = $this->actingAs($this->admin)->patch("/admin/users/{$targetUser->id}", [
            'role' => 'superadmin',
        ]);

        $response->assertSessionHasErrors('role');
    }

    public function test_admin_bisa_menghapus_user_lain(): void
    {
        $targetUser = User::factory()->user()->create();

        $response = $this->actingAs($this->admin)->delete("/admin/users/{$targetUser->id}");

        $response->assertRedirect(route('admin.users.index'));
        $this->assertDatabaseMissing('users', ['id' => $targetUser->id]);
    }

    public function test_admin_tidak_bisa_menghapus_diri_sendiri(): void
    {
        $response = $this->actingAs($this->admin)->delete("/admin/users/{$this->admin->id}");

        $response->assertRedirect();
        $this->assertDatabaseHas('users', ['id' => $this->admin->id]);
    }
}
