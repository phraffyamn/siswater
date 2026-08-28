<?php

namespace Database\Seeders;

use App\Models\AktivitasLog;
use App\Models\PermintaanItem;
use App\Models\PermintaanWarkah;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name'      => 'Administrator Sistem',
            'email'     => 'admin@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'admin',
            'nip'       => '197001011990011001',
            'jabatan'   => 'Administrator Sistem',
            'is_active' => true,
        ]);

        $pps1 = User::create([
            'name'      => 'Budi Santoso, S.H.',
            'email'     => 'pps@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'pps',
            'nip'       => '198505152010011001',
            'jabatan'   => 'Kepala Seksi Pengendalian & Sengketa',
            'is_active' => true,
        ]);

        $pps2 = User::create([
            'name'      => 'Dewi Rahayu, S.H.',
            'email'     => 'pps2@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'pps',
            'nip'       => '199001202015012002',
            'jabatan'   => 'Staf Penanganan Sengketa',
            'is_active' => true,
        ]);

        $phpt = User::create([
            'name'      => 'Ahmad Fauzi, S.T.',
            'email'     => 'phpt@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'phpt',
            'nip'       => '198803102012031002',
            'jabatan'   => 'Kepala Seksi Penetapan Hak & Pendaftaran',
            'is_active' => true,
        ]);

        $sp = User::create([
            'name'      => 'Rizky Pratama, S.T.',
            'email'     => 'sp@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'sp',
            'nip'       => '199203142016031003',
            'jabatan'   => 'Kepala Seksi Survei dan Pengukuran',
            'is_active' => true,
        ]);

        $tu = User::create([
            'name'      => 'Siti Nurhaliza, S.E.',
            'email'     => 'tu@siswater.id',
            'password'  => Hash::make('password123'),
            'role'      => 'tu',
            'nip'       => '198712051012022001',
            'jabatan'   => 'Kepala Sub Bagian Tata Usaha',
            'is_active' => true,
        ]);

        $this->buatPermintaan($pps1, $tu, $phpt, 'selesai');
        $this->buatPermintaan($pps1, $tu, $phpt, 'warkah_tersedia');
        $this->buatPermintaan($pps1, $tu, null, 'disetujui_tu');
        $this->buatPermintaan($pps1, null, null, 'menunggu_tu');
        $this->buatPermintaan($pps2, $tu, null, 'ditolak_tu');
        $this->buatPermintaan($pps2, null, null, 'menunggu_tu', now()->subDays(8));

        // Permintaan yang ditujukan ke Seksi Survei dan Pengukuran.
        $this->buatPermintaan($pps2, $tu, $sp, 'warkah_tersedia', null, 'sp');
        $this->buatPermintaan($pps1, $tu, null, 'disetujui_tu', null, 'sp');
    }

    private function buatPermintaan($pemohon, $tu, $petugas, $status, $deadline = null, $seksi = 'phpt'): void
    {
        $perihals = [
            'Permohonan Warkah untuk Penanganan Sengketa Batas Tanah',
            'Permintaan Dokumen Warkah Kepemilikan Lahan Sengketa',
            'Peminjaman Warkah untuk Penyelesaian Sengketa Tanah Warisan',
            'Permohonan Warkah Sertipikat Hak Milik atas Nama Sengketa',
        ];
        if ($seksi === 'sp') {
            $perihals = [
                'Permintaan Surat Ukur untuk Sengketa Batas Bidang',
                'Permohonan Gambar Ukur dan Peta Bidang Tanah Bersengketa',
            ];
        }
        $kota = ['Bandung', 'Bogor', 'Bekasi', 'Depok', 'Tangerang', 'Surabaya'];


        $data = [
            'nomor_nota'         => PermintaanWarkah::generateNomorNota(),
            'pemohon_id'         => $pemohon->id,
            'perihal'            => $perihals[array_rand($perihals)],
            'seksi_tujuan'       => $seksi,
            'keterangan'         => 'Dalam rangka penanganan sengketa pertanahan di ' . $kota[array_rand($kota)] . ', diperlukan dokumen warkah sebagai bahan pemeriksaan.',
            'status'             => $status,
            'tanggal_permintaan' => now()->subDays(rand(1, 15)),
            'deadline'           => $deadline ?? now()->addDays(rand(3, 14)),
        ];

        if ($tu && in_array($status, ['disetujui_tu', 'ditolak_tu', 'diproses_phpt', 'warkah_tersedia', 'dikembalikan', 'selesai'])) {
            $data['approved_by_tu'] = $tu->id;
            $data['approved_at_tu'] = now()->subDays(rand(1, 5));
            $data['catatan_tu']     = $status === 'ditolak_tu'
                ? 'Permintaan tidak memenuhi syarat administrasi.'
                : 'Permintaan disetujui. Seksi tujuan segera memproses.';
        }

        if ($petugas && in_array($status, ['diproses_phpt', 'warkah_tersedia', 'dikembalikan', 'selesai'])) {
            $data['processed_by_phpt'] = $petugas->id;
            $data['processed_at_phpt'] = now()->subDays(rand(1, 3));
            $data['catatan_phpt']      = 'Warkah telah disiapkan dan diupload dalam format digital.';
        }

        if (in_array($status, ['dikembalikan', 'selesai'])) {
            $data['dikembalikan_at']      = now()->subDay();
            $data['catatan_pengembalian'] = 'Warkah dikembalikan dalam kondisi lengkap.';
        }

        $permintaan = PermintaanWarkah::create($data);

        $jenisWarkah = ['Sertifikat Hak Milik', 'Buku Tanah', 'Surat Ukur', 'SKPT', 'Peta Bidang', 'Gambar Ukur'];
        $namaOrang   = ['Susanto', 'Rina Wati', 'Hadi Purnomo', 'Sri Lestari', 'Bambang K.', 'Nurul Hidayah'];
        $desa        = ['Sukamaju', 'Cikarang', 'Rancaekek', 'Cibiru', 'Ujungberung', 'Arcamanik'];

        for ($i = 0; $i < rand(2, 4); $i++) {
            PermintaanItem::create([
                'permintaan_id'     => $permintaan->id,
                'nama_warkah'       => $jenisWarkah[array_rand($jenisWarkah)],
                'nomor_hak'         => 'No. ' . rand(100, 9999),
                'nama_pemegang_hak' => $namaOrang[array_rand($namaOrang)],
                'lokasi'            => 'Desa ' . $desa[array_rand($desa)] . ', Kab. Bandung',
                'keterangan'        => null,
            ]);
        }

        AktivitasLog::create([
            'permintaan_id'  => $permintaan->id,
            'user_id'        => $pemohon->id,
            'aksi'           => 'Nota Dinas Permintaan Warkah dibuat',
            'status_sebelum' => null,
            'status_sesudah' => 'menunggu_tu',
        ]);

        if ($tu && isset($data['approved_at_tu'])) {
            AktivitasLog::create([
                'permintaan_id'  => $permintaan->id,
                'user_id'        => $tu->id,
                'aksi'           => $status === 'ditolak_tu' ? 'Ditolak oleh Tata Usaha' : 'Disetujui oleh Tata Usaha',
                'catatan'        => $data['catatan_tu'],
                'status_sebelum' => 'menunggu_tu',
                'status_sesudah' => $status === 'ditolak_tu' ? 'ditolak_tu' : 'disetujui_tu',
            ]);
        }

        if ($phpt && isset($data['processed_at_phpt'])) {
            AktivitasLog::create([
                'permintaan_id'  => $permintaan->id,
                'user_id'        => $phpt->id,
                'aksi'           => 'Warkah digital diupload oleh PHPT (3 file)',
                'catatan'        => $data['catatan_phpt'],
                'status_sebelum' => 'disetujui_tu',
                'status_sesudah' => 'warkah_tersedia',
            ]);
        }
    }
}
