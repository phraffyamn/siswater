<?php

namespace App\Http\Controllers;

use App\Models\PermintaanWarkah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MonitoringController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = PermintaanWarkah::with(['pemohon', 'approvedByTu', 'processedByPhpt', 'files'])->latest();

        if ($user->isPPS()) {
            $query->where('pemohon_id', $user->id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('bulan')) {
            $query->whereMonth('created_at', $request->bulan);
        }
        if ($request->filled('tahun')) {
            $query->whereYear('created_at', $request->tahun);
        }

        $permintaan = $query->paginate(20)->withQueryString();

        $statsByStatus = PermintaanWarkah::when($user->isPPS(), fn($q) => $q->where('pemohon_id', $user->id))
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $overdueCount = PermintaanWarkah::when($user->isPPS(), fn($q) => $q->where('pemohon_id', $user->id))
            ->whereNotNull('deadline')->where('deadline', '<', now())
            ->whereNotIn('status', ['warkah_tersedia', 'dikembalikan', 'selesai'])
            ->count();

        return view('monitoring.index', compact('permintaan', 'statsByStatus', 'overdueCount', 'user'));
    }
}
