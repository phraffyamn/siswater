-- =====================================================================
-- SISWA-TER - Sistem Warkah Terintegrasi
-- Skema MySQL/MariaDB + akun dan data contoh
--
-- Skema ini setara dengan hasil `php artisan migrate` pada Laravel 12.
-- Impor lewat phpMyAdmin (tab Import) atau:
--     mysql -u PENGGUNA -p NAMA_BASIS_DATA < siswater-mysql.sql
--
-- PENTING: setelah mengimpor file ini, JANGAN menjalankan
-- `php artisan db:seed` - akun demo akan terduplikasi.
-- Kata sandi seluruh akun demo: password123
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';

-- ---------------------------------------------------------------------
-- Bersihkan bila tabel sudah ada
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `aktivitas_logs`;
DROP TABLE IF EXISTS `warkah_files`;
DROP TABLE IF EXISTS `permintaan_items`;
DROP TABLE IF EXISTS `permintaan_warkah`;
DROP TABLE IF EXISTS `sessions`;
DROP TABLE IF EXISTS `password_reset_tokens`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `cache_locks`;
DROP TABLE IF EXISTS `cache`;
DROP TABLE IF EXISTS `failed_jobs`;
DROP TABLE IF EXISTS `job_batches`;
DROP TABLE IF EXISTS `jobs`;
DROP TABLE IF EXISTS `migrations`;

-- ---------------------------------------------------------------------
-- migrations - dicatat supaya `php artisan migrate` tidak mengulang
-- ---------------------------------------------------------------------
CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `migrations` (`migration`, `batch`) VALUES
('0001_01_01_000000_create_users_table', 1),
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2024_01_01_000001_update_users_table', 1),
('2024_01_01_000002_create_permintaan_warkah_table', 1),
('2024_01_01_000003_create_permintaan_items_table', 1),
('2024_01_01_000004_create_warkah_files_table', 1),
('2024_01_01_000005_create_aktivitas_logs_table', 1),
('2024_01_02_000001_tambah_seksi_survei_pengukuran', 1);

-- ---------------------------------------------------------------------
-- users
-- ---------------------------------------------------------------------
CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'pps',
  `nip` varchar(20) DEFAULT NULL,
  `jabatan` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text,
  `payload` longtext NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- cache dan queue (dipakai karena SESSION/CACHE/QUEUE memakai database)
-- ---------------------------------------------------------------------
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` smallint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- permintaan_warkah
-- ---------------------------------------------------------------------
CREATE TABLE `permintaan_warkah` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `nomor_nota` varchar(255) NOT NULL,
  `pemohon_id` bigint UNSIGNED NOT NULL,
  `perihal` varchar(255) NOT NULL,
  `seksi_tujuan` varchar(10) NOT NULL DEFAULT 'phpt',
  `keterangan` text,
  `status` enum('menunggu_tu','disetujui_tu','ditolak_tu','diproses_phpt','warkah_tersedia','dikembalikan','selesai') NOT NULL DEFAULT 'menunggu_tu',
  `tanggal_permintaan` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `deadline` timestamp NULL DEFAULT NULL,
  `approved_by_tu` bigint UNSIGNED DEFAULT NULL,
  `approved_at_tu` timestamp NULL DEFAULT NULL,
  `catatan_tu` text,
  `processed_by_phpt` bigint UNSIGNED DEFAULT NULL,
  `processed_at_phpt` timestamp NULL DEFAULT NULL,
  `catatan_phpt` text,
  `dikembalikan_at` timestamp NULL DEFAULT NULL,
  `catatan_pengembalian` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permintaan_warkah_nomor_nota_unique` (`nomor_nota`),
  KEY `permintaan_warkah_pemohon_id_foreign` (`pemohon_id`),
  KEY `permintaan_warkah_approved_by_tu_foreign` (`approved_by_tu`),
  KEY `permintaan_warkah_processed_by_phpt_foreign` (`processed_by_phpt`),
  CONSTRAINT `permintaan_warkah_pemohon_id_foreign`
    FOREIGN KEY (`pemohon_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `permintaan_warkah_approved_by_tu_foreign`
    FOREIGN KEY (`approved_by_tu`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `permintaan_warkah_processed_by_phpt_foreign`
    FOREIGN KEY (`processed_by_phpt`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- permintaan_items
-- ---------------------------------------------------------------------
CREATE TABLE `permintaan_items` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permintaan_id` bigint UNSIGNED NOT NULL,
  `nama_warkah` varchar(255) NOT NULL,
  `nomor_hak` varchar(255) DEFAULT NULL,
  `nama_pemegang_hak` varchar(255) DEFAULT NULL,
  `lokasi` varchar(255) DEFAULT NULL,
  `keterangan` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `permintaan_items_permintaan_id_foreign` (`permintaan_id`),
  CONSTRAINT `permintaan_items_permintaan_id_foreign`
    FOREIGN KEY (`permintaan_id`) REFERENCES `permintaan_warkah` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- warkah_files
-- ---------------------------------------------------------------------
CREATE TABLE `warkah_files` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permintaan_id` bigint UNSIGNED NOT NULL,
  `permintaan_item_id` bigint UNSIGNED DEFAULT NULL,
  `nama_file` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(20) NOT NULL,
  `file_size` bigint NOT NULL DEFAULT '0',
  `uploaded_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `warkah_files_permintaan_id_foreign` (`permintaan_id`),
  KEY `warkah_files_permintaan_item_id_foreign` (`permintaan_item_id`),
  KEY `warkah_files_uploaded_by_foreign` (`uploaded_by`),
  CONSTRAINT `warkah_files_permintaan_id_foreign`
    FOREIGN KEY (`permintaan_id`) REFERENCES `permintaan_warkah` (`id`) ON DELETE CASCADE,
  CONSTRAINT `warkah_files_permintaan_item_id_foreign`
    FOREIGN KEY (`permintaan_item_id`) REFERENCES `permintaan_items` (`id`) ON DELETE SET NULL,
  CONSTRAINT `warkah_files_uploaded_by_foreign`
    FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- aktivitas_logs
-- ---------------------------------------------------------------------
CREATE TABLE `aktivitas_logs` (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `permintaan_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `aksi` varchar(255) NOT NULL,
  `catatan` text,
  `status_sebelum` varchar(255) DEFAULT NULL,
  `status_sesudah` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `aktivitas_logs_permintaan_id_foreign` (`permintaan_id`),
  KEY `aktivitas_logs_user_id_foreign` (`user_id`),
  CONSTRAINT `aktivitas_logs_permintaan_id_foreign`
    FOREIGN KEY (`permintaan_id`) REFERENCES `permintaan_warkah` (`id`) ON DELETE CASCADE,
  CONSTRAINT `aktivitas_logs_user_id_foreign`
    FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
-- DATA CONTOH
-- Kata sandi seluruh akun: password123
-- =====================================================================

INSERT INTO `users`
  (`id`,`name`,`email`,`role`,`nip`,`jabatan`,`is_active`,`password`,`created_at`,`updated_at`) VALUES
(1,'Administrator Sistem','admin@siswater.id','admin','197001011990011001','Administrator Sistem',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00'),
(2,'Budi Santoso, S.H.','pps@siswater.id','pps','198505152010011001','Kepala Seksi Pengendalian & Sengketa',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00'),
(3,'Dewi Rahayu, S.H.','pps2@siswater.id','pps','199001202015012002','Staf Penanganan Sengketa',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00'),
(4,'Ahmad Fauzi, S.T.','phpt@siswater.id','phpt','198803102012031002','Kepala Seksi Penetapan Hak & Pendaftaran',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00'),
(5,'Siti Nurhaliza, S.E.','tu@siswater.id','tu','198712051012022001','Kepala Sub Bagian Tata Usaha',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00'),
(6,'Rizky Pratama, S.T.','sp@siswater.id','sp','199203142016031003','Kepala Seksi Survei dan Pengukuran',1,'$2y$12$WLGftE0oSTtTgxzcR.Vc/eWPIoYHfPqIQkgtfn1q2JXXEDAhEYbei','2026-08-01 08:00:00','2026-08-01 08:00:00');

INSERT INTO `permintaan_warkah`
  (`id`,`nomor_nota`,`pemohon_id`,`perihal`,`seksi_tujuan`,`keterangan`,`status`,`tanggal_permintaan`,`deadline`,
   `approved_by_tu`,`approved_at_tu`,`catatan_tu`,`processed_by_phpt`,`processed_at_phpt`,`catatan_phpt`,
   `dikembalikan_at`,`catatan_pengembalian`,`created_at`,`updated_at`) VALUES
(1,'ND-PPS/08/2026/0001',2,'Permohonan Warkah untuk Penanganan Sengketa Batas Tanah',
 'phpt','Diperlukan untuk penelitian lapangan sengketa batas persil.','selesai',
 '2026-08-05 09:00:00','2026-08-12 09:00:00',
 5,'2026-08-05 13:20:00','Disetujui, silakan diproses.',
 4,'2026-08-07 10:15:00','Warkah lengkap, sudah dipindai.',
 '2026-08-14 15:30:00','Warkah dikembalikan dalam keadaan utuh.',
 '2026-08-05 09:00:00','2026-08-14 15:30:00'),
(2,'ND-PPS/08/2026/0002',2,'Peminjaman Warkah untuk Penyelesaian Sengketa Tanah Warisan',
 'phpt','Berkas dibutuhkan untuk gelar kasus awal.','warkah_tersedia',
 '2026-08-15 08:30:00','2026-08-22 08:30:00',
 5,'2026-08-15 11:00:00','Disetujui.',
 4,'2026-08-17 14:45:00','Dua berkas telah diunggah.',
 NULL,NULL,'2026-08-15 08:30:00','2026-08-17 14:45:00'),
(3,'ND-PPS/08/2026/0003',2,'Permohonan Warkah Sertipikat Hak Milik atas Nama Sengketa',
 'sp','Untuk keperluan telaahan staf.','disetujui_tu',
 '2026-08-24 10:00:00','2026-08-31 10:00:00',
 5,'2026-08-24 15:10:00','Disetujui, mohon segera disiapkan.',
 NULL,NULL,NULL,NULL,NULL,'2026-08-24 10:00:00','2026-08-24 15:10:00'),
(4,'ND-PPS/08/2026/0004',3,'Permintaan Dokumen Warkah Kepemilikan Lahan Sengketa',
 'phpt','Menunggu verifikasi Tata Usaha.','menunggu_tu',
 '2026-08-27 09:20:00','2026-09-03 09:20:00',
 NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-27 09:20:00','2026-08-27 09:20:00'),
(5,'ND-PPS/08/2026/0005',3,'Peminjaman Warkah Perkara Tumpang Tindih Sertipikat',
 'sp','Sudah melewati tenggat, untuk menguji tampilan monitoring.','menunggu_tu',
 '2026-08-10 08:00:00','2026-08-17 08:00:00',
 NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'2026-08-10 08:00:00','2026-08-10 08:00:00'),
(6,'ND-PPS/08/2026/0006',3,'Permohonan Warkah Bidang Tanah Terindikasi Ganda',
 'phpt','Ditolak karena identitas bidang belum lengkap.','ditolak_tu',
 '2026-08-18 13:00:00','2026-08-25 13:00:00',
 5,'2026-08-18 16:40:00','Nomor hak belum dicantumkan, mohon diajukan ulang.',
 NULL,NULL,NULL,NULL,NULL,'2026-08-18 13:00:00','2026-08-18 16:40:00');

INSERT INTO `permintaan_items`
  (`permintaan_id`,`nama_warkah`,`nomor_hak`,`nama_pemegang_hak`,`lokasi`,`keterangan`,`created_at`,`updated_at`) VALUES
(1,'Buku Tanah Hak Milik','HM 01234','Sutrisno','Desa Curug, Kec. Curug','Warkah asli','2026-08-05 09:00:00','2026-08-05 09:00:00'),
(1,'Surat Ukur','SU 00456/2011','Sutrisno','Desa Curug, Kec. Curug',NULL,'2026-08-05 09:00:00','2026-08-05 09:00:00'),
(2,'Buku Tanah Hak Milik','HM 04567','Ahmad Yusuf','Desa Cikupa, Kec. Cikupa','Terkait sengketa waris','2026-08-15 08:30:00','2026-08-15 08:30:00'),
(2,'Warkah Peralihan Hak','AJB 22/2019','Ahmad Yusuf','Desa Cikupa, Kec. Cikupa',NULL,'2026-08-15 08:30:00','2026-08-15 08:30:00'),
(3,'Buku Tanah Hak Milik','HM 07788','Ratna Sari','Desa Pasirjaya, Kec. Jayanti',NULL,'2026-08-24 10:00:00','2026-08-24 10:00:00'),
(4,'Buku Tanah Hak Guna Bangunan','HGB 00321','PT Sinar Abadi','Desa Balaraja, Kec. Balaraja',NULL,'2026-08-27 09:20:00','2026-08-27 09:20:00'),
(5,'Surat Ukur','SU 00987/2015','Hendra Wijaya','Desa Kresek, Kec. Kresek',NULL,'2026-08-10 08:00:00','2026-08-10 08:00:00'),
(6,'Buku Tanah Hak Milik',NULL,'Marlina','Desa Sukamulya, Kec. Sukamulya','Nomor hak belum diketahui','2026-08-18 13:00:00','2026-08-18 13:00:00');

INSERT INTO `aktivitas_logs`
  (`permintaan_id`,`user_id`,`aksi`,`catatan`,`status_sebelum`,`status_sesudah`,`created_at`,`updated_at`) VALUES
(1,2,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-05 09:00:00','2026-08-05 09:00:00'),
(1,5,'Menyetujui permintaan','Disetujui, silakan diproses.','menunggu_tu','disetujui_tu','2026-08-05 13:20:00','2026-08-05 13:20:00'),
(1,4,'Mengunggah warkah','Warkah lengkap, sudah dipindai.','disetujui_tu','warkah_tersedia','2026-08-07 10:15:00','2026-08-07 10:15:00'),
(1,2,'Mengembalikan warkah','Warkah dikembalikan dalam keadaan utuh.','warkah_tersedia','selesai','2026-08-14 15:30:00','2026-08-14 15:30:00'),
(2,2,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-15 08:30:00','2026-08-15 08:30:00'),
(2,5,'Menyetujui permintaan','Disetujui.','menunggu_tu','disetujui_tu','2026-08-15 11:00:00','2026-08-15 11:00:00'),
(2,4,'Mengunggah warkah','Dua berkas telah diunggah.','disetujui_tu','warkah_tersedia','2026-08-17 14:45:00','2026-08-17 14:45:00'),
(3,2,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-24 10:00:00','2026-08-24 10:00:00'),
(3,5,'Menyetujui permintaan','Disetujui, mohon segera disiapkan.','menunggu_tu','disetujui_tu','2026-08-24 15:10:00','2026-08-24 15:10:00'),
(4,3,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-27 09:20:00','2026-08-27 09:20:00'),
(5,3,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-10 08:00:00','2026-08-10 08:00:00'),
(6,3,'Mengajukan permintaan',NULL,NULL,'menunggu_tu','2026-08-18 13:00:00','2026-08-18 13:00:00'),
(6,5,'Menolak permintaan','Nomor hak belum dicantumkan, mohon diajukan ulang.','menunggu_tu','ditolak_tu','2026-08-18 16:40:00','2026-08-18 16:40:00');

SET FOREIGN_KEY_CHECKS = 1;
