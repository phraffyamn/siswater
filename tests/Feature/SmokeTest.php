<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_tamu_diarahkan_ke_halaman_login(): void
    {
        $this->get('/')->assertRedirect(route('login'));
    }

    public function test_halaman_login_terbuka(): void
    {
        $this->get('/login')->assertOk();
    }

    public function test_endpoint_kesehatan_menyala(): void
    {
        $this->get('/up')->assertOk();
    }

    public function test_pengguna_aktif_dapat_masuk_ke_dasbor(): void
    {
        $pengguna = $this->buatPengguna();

        $this->post('/login', [
            'email'    => 'uji@siswater.id',
            'password' => 'password123',
        ])->assertRedirect(route('dashboard'));

        $this->actingAs($pengguna)->get('/dashboard')->assertOk();
    }

    public function test_pengguna_nonaktif_ditolak(): void
    {
        $this->buatPengguna(aktif: false);

        $this->post('/login', [
            'email'    => 'uji@siswater.id',
            'password' => 'password123',
        ])->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_tamu_tidak_bisa_membuka_dasbor(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
    }

    private function buatPengguna(bool $aktif = true): User
    {
        return User::create([
            'name'      => 'Penguji',
            'email'     => 'uji@siswater.id',
            'password'  => 'password123',
            'role'      => 'pps',
            'nip'       => '199001012020011001',
            'jabatan'   => 'Staf Penanganan Sengketa',
            'is_active' => $aktif,
        ]);
    }
}
