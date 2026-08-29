<?php

namespace Database\Seeders;

use App\Models\AktivitasLog;
use App\Models\PermintaanItem;
use App\Models\PermintaanWarkah;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Sepuluh sampel permintaan warkah untuk demo.
 *
 * Bentuk perkara mengikuti rekapitulasi sengketa 2025-2026, tetapi SELURUH
 * identitas sudah diganti: nama pemegang hak adalah nama samaran yang telah
 * diperiksa tidak muncul pada berkas sumber, nomor sertipikat digeser
 * sehingga tidak merujuk bidang mana pun, dan tidak ada nama pemohon,
 * termohon, maupun kuasa hukum yang tersisa. Aman untuk demo publik.
 *
 * Jalankan:
 *   php artisan db:seed --class=SampelPengaduanSeeder
 */
class SampelPengaduanSeeder extends Seeder
{
    public function run(): void
    {
        $pps  = User::where('role', 'pps')->first();
        $tu   = User::where('role', 'tu')->first();
        $phpt = User::where('role', 'phpt')->first();
        $sp   = User::where('role', 'sp')->first();

        if (! $pps) {
            $this->command->error('Akun PPS belum ada. Jalankan php artisan db:seed lebih dulu.');

            return;
        }

        foreach ($this->sampel() as $s) {
            $petugas  = $s['seksi_tujuan'] === 'sp' ? $sp : $phpt;
            $diajukan = now()->subDays($s['hari_lalu']);

            $data = [
                'nomor_nota'         => PermintaanWarkah::generateNomorNota(),
                'pemohon_id'         => $pps->id,
                'perihal'            => $s['perihal'],
                'seksi_tujuan'       => $s['seksi_tujuan'],
                'keterangan'         => $s['keterangan'],
                'status'             => $s['status'],
                'tanggal_permintaan' => $diajukan,
                'deadline'           => $diajukan->copy()->addDays(7),
                'created_at'         => $diajukan,
                'updated_at'         => $diajukan,
            ];

            if ($tu && $s['status'] !== 'menunggu_tu') {
                $data['approved_by_tu'] = $tu->id;
                $data['approved_at_tu'] = $diajukan->copy()->addHours(5);
                $data['catatan_tu']     = $s['status'] === 'ditolak_tu'
                    ? 'Identitas bidang belum lengkap, mohon dilengkapi dan diajukan ulang.'
                    : 'Disetujui, silakan disiapkan oleh seksi terkait.';
            }

            if ($petugas && in_array($s['status'], ['warkah_tersedia', 'selesai'], true)) {
                $data['processed_by_phpt'] = $petugas->id;
                $data['processed_at_phpt'] = $diajukan->copy()->addDays(2);
                $data['catatan_phpt']      = 'Warkah telah dipindai dan diunggah.';
            }

            if ($s['status'] === 'selesai') {
                $data['dikembalikan_at']      = $diajukan->copy()->addDays(5);
                $data['catatan_pengembalian'] = 'Warkah telah digunakan untuk mediasi.';
            }

            $permintaan = PermintaanWarkah::create($data);

            PermintaanItem::create(array_merge(
                ['permintaan_id' => $permintaan->id],
                $s['item']
            ));

            AktivitasLog::create([
                'permintaan_id'  => $permintaan->id,
                'user_id'        => $pps->id,
                'aksi'           => 'Mengajukan permintaan',
                'status_sebelum' => null,
                'status_sesudah' => 'menunggu_tu',
                'created_at'     => $diajukan,
                'updated_at'     => $diajukan,
            ]);
        }

        $this->command->info('10 sampel permintaan warkah berhasil ditambahkan.');
    }

    private function sampel(): array
    {
        return [
            [
                'status'       => 'selesai',
                'seksi_tujuan' => 'sp',
                'hari_lalu'    => 28,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Kepemilikan',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa kepemilikan. Objek: SHGB 982/Sodong.',
                'item'         => [
                    'nama_warkah'       => 'Surat Ukur',
                    'nomor_hak'         => 'SHGB 982/Sodong',
                    'nama_pemegang_hak' => 'Bagas Wicaksana',
                    'lokasi'            => 'Desa Sodong, Kec. Tigaraksa',
                    'keterangan'        => 'Luas kurang lebih 985 m2',
                ],
            ],
            [
                'status'       => 'warkah_tersedia',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 26,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Permohonan Pembatalan Sertipikat',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara permohonan pembatalan sertipikat. Objek: Sertipikat Hak Milik Nomor 2116/Pangadegan.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Milik',
                    'nomor_hak'         => 'Sertipikat Hak Milik Nomor 2116/Pangadegan',
                    'nama_pemegang_hak' => 'Ratih Prameswari',
                    'lokasi'            => 'Kel. Pangadegan Kec. Pasarkemis',
                    'keterangan'        => 'Luas kurang lebih 2.408 m2 m2',
                ],
            ],
            [
                'status'       => 'warkah_tersedia',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 24,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Tumpang Tindih (Overlap)',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa tumpang tindih (overlap). Objek: SHM 471/Rawakidang.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Milik',
                    'nomor_hak'         => 'SHM 471/Rawakidang',
                    'nama_pemegang_hak' => 'Yudha Nugraha',
                    'lokasi'            => 'Desa Rawakidang Kec. Sukadiri',
                    'keterangan'        => 'Luas kurang lebih 5.820 m2',
                ],
            ],
            [
                'status'       => 'disetujui_tu',
                'seksi_tujuan' => 'sp',
                'hari_lalu'    => 22,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Kepemilikan',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa kepemilikan. Objek: Berkas Pengukuran.',
                'item'         => [
                    'nama_warkah'       => 'Surat Ukur',
                    'nomor_hak'         => 'Berkas Pengukuran',
                    'nama_pemegang_hak' => 'Intan Maharani',
                    'lokasi'            => 'Desa Mekarsari Kec. Jambe',
                    'keterangan'        => 'Luas kurang lebih 1.050 m2',
                ],
            ],
            [
                'status'       => 'disetujui_tu',
                'seksi_tujuan' => 'sp',
                'hari_lalu'    => 20,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Kepemilikan',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa kepemilikan. Objek: Girik No. C. 480.',
                'item'         => [
                    'nama_warkah'       => 'Surat Ukur',
                    'nomor_hak'         => 'Girik No. C. 480',
                    'nama_pemegang_hak' => 'Fajar Adiputra',
                    'lokasi'            => 'Desa Pangadegan',
                    'keterangan'        => 'Luas kurang lebih 787 m2',
                ],
            ],
            [
                'status'       => 'menunggu_tu',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 18,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Dugaan Penyerobotan Tanah',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara dugaan penyerobotan tanah. Objek: TMA Desa Salembaran Jaya.',
                'item'         => [
                    'nama_warkah'       => 'Warkah Tanah Milik Adat',
                    'nomor_hak'         => 'TMA Desa Salembaran Jaya',
                    'nama_pemegang_hak' => 'Larasati Wijoyo',
                    'lokasi'            => 'Desa Salembaran Jaya',
                    'keterangan'        => '',
                ],
            ],
            [
                'status'       => 'menunggu_tu',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 16,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Tumpang Tindih (Overlap)',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa tumpang tindih (overlap). Objek: SHM 4864/Bantar Panjang.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Milik',
                    'nomor_hak'         => 'SHM 4864/Bantar Panjang',
                    'nama_pemegang_hak' => 'Gilang Baskoro',
                    'lokasi'            => 'Desa Bantar Panjang',
                    'keterangan'        => 'Luas kurang lebih 2.947 m2',
                ],
            ],
            [
                'status'       => 'ditolak_tu',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 14,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Penggantian Sertipikat Hilang',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara penggantian sertipikat hilang. Objek: SHM 5609.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Milik',
                    'nomor_hak'         => 'SHM 5609',
                    'nama_pemegang_hak' => 'PT Adhi Lestari Makmur',
                    'lokasi'            => 'Desa Sukamantri',
                    'keterangan'        => '',
                ],
            ],
            [
                'status'       => 'menunggu_tu',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 12,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Peralihan Hak',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa peralihan hak. Objek: SHGB 8955/Sukamantri.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Guna Bangunan',
                    'nomor_hak'         => 'SHGB 8955/Sukamantri',
                    'nama_pemegang_hak' => 'Anindya Prawira',
                    'lokasi'            => 'Desa Sukamantri',
                    'keterangan'        => '',
                ],
            ],
            [
                'status'       => 'warkah_tersedia',
                'seksi_tujuan' => 'phpt',
                'hari_lalu'    => 10,
                'perihal'      => 'Permohonan Warkah untuk Penanganan Sengketa Tumpang Tindih (Overlap)',
                'keterangan'   => 'Diperlukan untuk penelitian berkas dan persiapan mediasi perkara sengketa tumpang tindih (overlap). Objek: SHGB 3607/Kadu Agung.',
                'item'         => [
                    'nama_warkah'       => 'Buku Tanah Hak Guna Bangunan',
                    'nomor_hak'         => 'SHGB 3607/Kadu Agung',
                    'nama_pemegang_hak' => 'PT Cendana Bumi Nusantara',
                    'lokasi'            => 'Desa Kadu Agung',
                    'keterangan'        => 'Luas kurang lebih 950 m2',
                ],
            ],
        ];
    }
}
