<?php

namespace Tests\Feature;

use App\Models\PermintaanWarkah;
use App\Models\User;
use App\Models\WarkahFile;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SeksiDanPratinjauTest extends TestCase
{
    use RefreshDatabase;

    public function test_pengguna_seksi_sp_dapat_masuk(): void
    {
        $sp = $this->buatPengguna('sp', 'sp@uji.id');

        $this->post('/login', ['email' => 'sp@uji.id', 'password' => 'password123'])
             ->assertRedirect(route('dashboard'));

        $this->actingAs($sp)->get('/dashboard')->assertOk();
    }

    public function test_label_peran_sp_tampil_utuh(): void
    {
        $sp = $this->buatPengguna('sp', 'sp2@uji.id');

        $this->assertSame('Survei dan Pengukuran', $sp->role_label);
        $this->assertSame('SP', $sp->role_singkat);
        $this->assertTrue($sp->isSP());
        $this->assertTrue($sp->isPetugasWarkah());
        $this->assertFalse($sp->isPHPT());
    }

    public function test_pps_dapat_menujukan_permintaan_ke_sp(): void
    {
        $pps = $this->buatPengguna('pps', 'pps@uji.id');

        $this->actingAs($pps)->post('/permintaan', [
            'perihal'      => 'Permintaan Surat Ukur untuk Sengketa Batas',
            'seksi_tujuan' => 'sp',
            'nama_warkah'  => ['Surat Ukur'],
        ])->assertRedirect();

        $this->assertDatabaseHas('permintaan_warkah', [
            'perihal'      => 'Permintaan Surat Ukur untuk Sengketa Batas',
            'seksi_tujuan' => 'sp',
        ]);
    }

    public function test_seksi_tujuan_wajib_diisi(): void
    {
        $pps = $this->buatPengguna('pps', 'pps2@uji.id');

        $this->actingAs($pps)->post('/permintaan', [
            'perihal'     => 'Tanpa seksi tujuan',
            'nama_warkah' => ['Buku Tanah'],
        ])->assertSessionHasErrors('seksi_tujuan');
    }

    public function test_phpt_tidak_bisa_mengunggah_ke_permintaan_milik_sp(): void
    {
        Storage::fake('public');

        $phpt = $this->buatPengguna('phpt', 'phpt@uji.id');
        $permintaan = $this->buatPermintaan('sp');

        $this->actingAs($phpt)
             ->post("/permintaan/{$permintaan->id}/upload", [
                 'files' => [UploadedFile::fake()->create('warkah.pdf', 100, 'application/pdf')],
             ])
             ->assertForbidden();
    }

    public function test_sp_dapat_mengunggah_ke_permintaan_miliknya(): void
    {
        Storage::fake('public');

        $sp = $this->buatPengguna('sp', 'sp3@uji.id');
        $permintaan = $this->buatPermintaan('sp');

        $this->actingAs($sp)
             ->post("/permintaan/{$permintaan->id}/upload", [
                 'files' => [UploadedFile::fake()->create('surat-ukur.pdf', 100, 'application/pdf')],
             ])
             ->assertRedirect();

        $this->assertDatabaseHas('warkah_files', ['permintaan_id' => $permintaan->id]);
        $this->assertSame('warkah_tersedia', $permintaan->fresh()->status);
    }

    public function test_pratinjau_pdf_dikirim_inline(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('warkah/1/contoh.pdf', '%PDF-1.4 uji');

        $pps  = $this->buatPengguna('pps', 'pps3@uji.id');
        $file = $this->buatBerkas('contoh.pdf', 'pdf', 'warkah/1/contoh.pdf');

        $respons = $this->actingAs($pps)->get(route('warkah.preview', $file));

        $respons->assertOk();
        $respons->assertHeader('content-type', 'application/pdf');
        $this->assertStringContainsString('inline', $respons->headers->get('content-disposition'));
    }

    public function test_pratinjau_zip_dialihkan_ke_unduhan(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('warkah/1/arsip.zip', 'PK uji');

        $pps  = $this->buatPengguna('pps', 'pps4@uji.id');
        $file = $this->buatBerkas('arsip.zip', 'zip', 'warkah/1/arsip.zip');

        $this->actingAs($pps)
             ->get(route('warkah.preview', $file))
             ->assertRedirect(route('warkah.download', $file));
    }

    public function test_tamu_tidak_bisa_melihat_pratinjau(): void
    {
        $file = $this->buatBerkas('rahasia.pdf', 'pdf', 'warkah/1/rahasia.pdf');

        $this->get(route('warkah.preview', $file))->assertRedirect(route('login'));
    }

    // ---------- pembantu ----------

    private function buatPengguna(string $peran, string $email): User
    {
        return User::create([
            'name'      => strtoupper($peran) . ' Penguji',
            'email'     => $email,
            'password'  => 'password123',
            'role'      => $peran,
            'nip'       => '199001012020011001',
            'jabatan'   => 'Penguji',
            'is_active' => true,
        ]);
    }

    private function buatPermintaan(string $seksi): PermintaanWarkah
    {
        $pemohon = User::where('role', 'pps')->first()
            ?? $this->buatPengguna('pps', 'pemohon-' . uniqid() . '@uji.id');

        return PermintaanWarkah::create([
            'nomor_nota'   => PermintaanWarkah::generateNomorNota(),
            'pemohon_id'   => $pemohon->id,
            'perihal'      => 'Permintaan uji',
            'seksi_tujuan' => $seksi,
            'status'       => 'disetujui_tu',
        ]);
    }

    private function buatBerkas(string $nama, string $jenis, string $path): WarkahFile
    {
        $permintaan = $this->buatPermintaan('phpt');

        return WarkahFile::create([
            'permintaan_id' => $permintaan->id,
            'nama_file'     => $nama,
            'file_path'     => $path,
            'file_type'     => $jenis,
            'file_size'     => 1024,
            'uploaded_by'   => $permintaan->pemohon_id,
        ]);
    }
}
