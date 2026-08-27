<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;

class ResetDemo extends Command
{
    protected $signature = 'siswater:reset-demo {--force : Jalankan tanpa meminta konfirmasi}';

    protected $description = 'Mengosongkan seluruh data lalu memuat ulang akun dan permintaan contoh';

    public function handle(): int
    {
        if (! $this->option('force')
            && ! $this->confirm('Seluruh data akan dihapus dan diganti data contoh. Lanjutkan?')) {
            $this->components->warn('Dibatalkan.');

            return self::SUCCESS;
        }

        $this->components->task('Menghapus berkas unggahan', function (): bool {
            $disk = Storage::disk('public');

            foreach ($disk->allFiles() as $berkas) {
                $disk->delete($berkas);
            }

            return true;
        });

        $this->components->task('Mengosongkan tabel', function (): bool {
            $tabel = [
                'aktivitas_logs',
                'warkah_files',
                'permintaan_items',
                'permintaan_warkah',
                'sessions',
                'users',
            ];

            Schema::disableForeignKeyConstraints();

            foreach ($tabel as $nama) {
                if (Schema::hasTable($nama)) {
                    DB::table($nama)->delete();
                }
            }

            Schema::enableForeignKeyConstraints();

            return true;
        });

        $this->components->task(
            'Memuat data contoh',
            fn (): bool => $this->callSilent('db:seed', ['--force' => true]) === self::SUCCESS
        );

        $this->newLine();
        $this->components->info('Data demo disegarkan. Masuk dengan pps@siswater.id / password123');

        return self::SUCCESS;
    }
}
