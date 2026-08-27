# SISWA-TER — Sistem Warkah Terintegrasi

Aplikasi peminjaman warkah dan buku tanah antar seksi di Kantor Pertanahan.
Permintaan diajukan oleh Seksi PPS, diverifikasi Tata Usaha, lalu disiapkan
dan diunggah oleh Seksi PHPT — seluruh perpindahan status tercatat dalam log
aktivitas sehingga posisi berkas selalu bisa ditelusuri.

Prototipe. Data contoh, bukan data pertanahan sebenarnya.

---

## Coba dalam 2 menit (Docker)

Cukup punya Docker. Tidak perlu PHP, Composer, atau Node.

```bash
git clone https://github.com/phraffyamn/siswater.git
cd siswater
docker compose up --build
```

Buka **http://localhost:8080**. Basis data, migrasi, dan data contoh
disiapkan otomatis saat kontainer pertama kali hidup.

Menghentikan: `Ctrl+C`. Menghapus data uji dan mulai bersih:
`docker compose down -v`.

## Akun demo

Kata sandi semua akun: `password123`

| Email | Peran | Bisa melakukan |
|---|---|---|
| `pps@siswater.id` | Pengendalian & Penanganan Sengketa | mengajukan permintaan, menerima & mengembalikan warkah |
| `tu@siswater.id` | Tata Usaha | menyetujui atau menolak permintaan |
| `phpt@siswater.id` | Penetapan Hak & Pendaftaran Tanah | menyiapkan dan mengunggah berkas warkah |
| `admin@siswater.id` | Administrator | seluruh peran di atas + kelola pengguna |

Alur paling cepat untuk dinilai: masuk sebagai **pps**, ajukan permintaan
baru → keluar, masuk sebagai **tu**, setujui → keluar, masuk sebagai
**phpt**, unggah berkas → kembali sebagai **pps**, unduh dan kembalikan.
Halaman Monitoring menampilkan durasi tiap tahap dan permintaan yang
melewati tenggat.

## Menjalankan tanpa Docker

Butuh PHP 8.2+, Composer, dan Node 20+.

```bash
composer setup      # install, salin .env, buat kunci, migrasi, build aset
php artisan db:seed # data contoh + akun demo
php artisan serve
```

Pengguna Windows bisa langsung menjalankan `JALANKAN.bat` setelah
`composer setup` selesai sekali.

## Menerbitkan sebagai demo publik (Railway)

Repositori sudah berisi `Dockerfile` dan `railway.json`.

1. Buat service baru dari repositori ini — Railway membaca `railway.json`
   dan memakai Dockerfile.
2. Tambahkan **Volume** dengan mount path `/data`. Tanpa ini, basis data
   dan berkas unggahan hilang setiap kali deploy ulang.
3. Isi variabel sesuai `railway.env.example`. `APP_KEY` wajib —
   hasilkan dengan `php artisan key:generate --show`.
4. Opsional: setel `RUN_SCHEDULER=true` agar data demo disegarkan otomatis.
5. Deploy. Pemeriksaan kesehatan memakai `/up`.

## Menyegarkan data demo

Setelah banyak dicoba orang, data contoh biasanya berantakan. Kembalikan
ke kondisi awal kapan saja:

```bash
php artisan siswater:reset-demo
```

Lewat Docker: `docker compose exec app php artisan siswater:reset-demo --force`

Untuk demo publik, penyegaran bisa berjalan otomatis tiap hari pukul
01.00 WIB — setel variabel `RUN_SCHEDULER=true` pada layanan. Tanpa
variabel itu penjadwal tidak dinyalakan dan reset hanya manual.

## Teknologi

Laravel 12 · PHP 8.3 · SQLite · Blade · Tailwind CSS 4 · Vite ·
FrankenPHP sebagai runtime produksi.

## Struktur singkat

```
app/Http/Controllers/   Auth, Dashboard, Permintaan, Monitoring, Admin
app/Models/             User, PermintaanWarkah, PermintaanItem,
                        WarkahFile, AktivitasLog
database/seeders/       akun demo + 6 permintaan contoh lintas status
docker/entrypoint.sh    migrasi, seed bila kosong, cache konfigurasi
resources/views/        antarmuka Blade
```

## Status permintaan

`menunggu_tu` → `disetujui_tu` / `ditolak_tu` → `warkah_tersedia` →
`selesai`

Setiap perpindahan menulis entri pada `aktivitas_logs` berisi waktu,
pengguna, dan tindakan.
