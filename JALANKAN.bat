@echo off
title SISWA-TER - Sistem Warkah Terintegrasi
echo.
echo  ====================================================
echo  |          SISWA-TER v1.0                         |
echo  |     Sistem Warkah Terintegrasi                  |
echo  |     Kementerian ATR/BPN                         |
echo  ====================================================
echo.
echo  Memulai server aplikasi...
echo.
echo  Akun Demo:
echo  - admin@siswater.id   (Admin)
echo  - pps@siswater.id     (Pengendalian & Sengketa)
echo  - phpt@siswater.id    (Penetapan Hak & Pendaftaran)
echo  - tu@siswater.id      (Tata Usaha)
echo  Password semua: password123
echo.
echo  Buka browser dan akses: http://localhost:8000
echo  Tekan Ctrl+C untuk menghentikan server
echo.
cd /d %~dp0
php artisan serve --port=8000
