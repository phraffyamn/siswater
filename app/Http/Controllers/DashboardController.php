<?php

namespace App\Http\Controllers;

use App\Models\PermintaanWarkah;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $stats = [];
        $recentPermintaan = collect();

        if ($user->isAdmin()) {
            $stats = [
                'total_permintaan'      => PermintaanWarkah::count(),
                'menunggu_tu'           => PermintaanWarkah::where('status', 'menunggu_tu')->count(),
                'diproses'              => PermintaanWarkah::whereIn('status', ['disetujui_tu', 'diproses_phpt'])->count(),
                'selesai'               => PermintaanWarkah::whereIn('status', ['warkah_tersedia', 'selesai'])->count(),
                'total_users'           => User::where('role', '!=', 'admin')->count(),
                'overdue'               => PermintaanWarkah::whereNotNull('deadline')
                                            ->where('deadline', '<', now())
                                            ->whereNotIn('status', ['warkah_tersedia', 'dikembalikan', 'selesai'])
                                            ->count(),
            ];
            $recentPermintaan = PermintaanWarkah::with(['pemohon'])->latest()->take(10)->get();
        } elseif ($user->isPPS()) {
            $stats = [
                'total_saya'    => PermintaanWarkah::where('pemohon_id', $user->id)->count(),
                'menunggu'      => PermintaanWarkah::where('pemohon_id', $user->id)
                                    ->whereIn('status', ['menunggu_tu', 'disetujui_tu', 'diproses_phpt'])->count(),
                'tersedia'      => PermintaanWarkah::where('pemohon_id', $user->id)
                                    ->where('status', 'warkah_tersedia')->count(),
                'overdue'       => PermintaanWarkah::where('pemohon_id', $user->id)
                                    ->whereNotNull('deadline')->where('deadline', '<', now())
                                    ->whereNotIn('status', ['warkah_tersedia', 'dikembalikan', 'selesai'])->count(),
            ];
            $recentPermintaan = PermintaanWarkah::where('pemohon_id', $user->id)->latest()->take(10)->get();
        } elseif ($user->isTU()) {
            $stats = [
                'menunggu_persetujuan'  => PermintaanWarkah::where('status', 'menunggu_tu')->count(),
                'disetujui'             => PermintaanWarkah::where('approved_by_tu', $user->id)->where('status', '!=', 'ditolak_tu')->count(),
                'ditolak'               => PermintaanWarkah::where('approved_by_tu', $user->id)->where('status', 'ditolak_tu')->count(),
                'total_bulan_ini'       => PermintaanWarkah::whereMonth('created_at', now()->month)->count(),
            ];
            $recentPermintaan = PermintaanWarkah::with(['pemohon'])->latest()->take(10)->get();
        } elseif ($user->isPetugasWarkah()) {
            // PHPT dan SP memakai papan yang sama, dibatasi pada permintaan
            // yang ditujukan ke seksi masing-masing.
            $seksi = $user->role;

            $stats = [
                'antrian_upload'        => PermintaanWarkah::where('seksi_tujuan', $seksi)
                                            ->where('status', 'disetujui_tu')->count(),
                'sedang_diproses'       => PermintaanWarkah::where('seksi_tujuan', $seksi)
                                            ->where('status', 'diproses_phpt')
                                            ->where('processed_by_phpt', $user->id)->count(),
                'selesai_diupload'      => PermintaanWarkah::where('processed_by_phpt', $user->id)
                                            ->where('status', 'warkah_tersedia')->count(),
                'total_bulan_ini'       => PermintaanWarkah::where('seksi_tujuan', $seksi)
                                            ->whereMonth('created_at', now()->month)
                                            ->where('status', 'warkah_tersedia')->count(),
            ];
            $recentPermintaan = PermintaanWarkah::with(['pemohon'])
                ->where('seksi_tujuan', $seksi)
                ->whereIn('status', ['disetujui_tu', 'diproses_phpt'])
                ->latest()->take(10)->get();
        }

        return view('dashboard', compact('stats', 'recentPermintaan', 'user'));
    }
}
