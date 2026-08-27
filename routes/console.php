<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Menyegarkan data demo publik setiap dini hari, supaya percobaan
// pengunjung tidak menumpuk. Hanya berjalan bila penjadwal aktif —
// setel RUN_SCHEDULER=true pada layanan yang menjalankan aplikasi.
Schedule::command('siswater:reset-demo --force')
    ->dailyAt('01:00')
    ->timezone('Asia/Jakarta')
    ->withoutOverlapping();
