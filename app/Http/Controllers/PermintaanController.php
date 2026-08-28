<?php

namespace App\Http\Controllers;

use App\Models\AktivitasLog;
use App\Models\PermintaanWarkah;
use App\Models\PermintaanItem;
use App\Models\WarkahFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PermintaanController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = PermintaanWarkah::with(['pemohon', 'files'])->latest();

        if ($user->isPPS()) {
            $query->where('pemohon_id', $user->id);
        } elseif ($user->isPetugasWarkah()) {
            $query->where('seksi_tujuan', $user->role)
                  ->whereIn('status', ['disetujui_tu', 'diproses_phpt', 'warkah_tersedia']);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $q = $request->search;
            $query->where(function ($sq) use ($q) {
                $sq->where('nomor_nota', 'like', "%$q%")
                   ->orWhere('perihal', 'like', "%$q%");
            });
        }

        $permintaan = $query->paginate(15)->withQueryString();

        return view('permintaan.index', compact('permintaan', 'user'));
    }

    public function create()
    {
        return view('permintaan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'perihal'       => 'required|string|max:255',
            'seksi_tujuan'  => 'required|in:phpt,sp',
            'keterangan'    => 'nullable|string',
            'deadline'      => 'nullable|date|after:today',
            'nama_warkah'   => 'required|array|min:1',
            'nama_warkah.*' => 'required|string|max:255',
        ]);

        $permintaan = PermintaanWarkah::create([
            'nomor_nota'         => PermintaanWarkah::generateNomorNota(),
            'pemohon_id'         => Auth::id(),
            'perihal'            => $request->perihal,
            'seksi_tujuan'       => $request->seksi_tujuan,
            'keterangan'         => $request->keterangan,
            'deadline'           => $request->deadline,
            'status'             => 'menunggu_tu',
            'tanggal_permintaan' => now(),
        ]);

        foreach ($request->nama_warkah as $i => $nama) {
            if (!empty($nama)) {
                PermintaanItem::create([
                    'permintaan_id'    => $permintaan->id,
                    'nama_warkah'      => $nama,
                    'nomor_hak'        => $request->nomor_hak[$i] ?? null,
                    'nama_pemegang_hak'=> $request->nama_pemegang_hak[$i] ?? null,
                    'lokasi'           => $request->lokasi[$i] ?? null,
                    'keterangan'       => $request->keterangan_item[$i] ?? null,
                ]);
            }
        }

        AktivitasLog::create([
            'permintaan_id'  => $permintaan->id,
            'user_id'        => Auth::id(),
            'aksi'           => 'Nota Dinas Permintaan Warkah dibuat',
            'status_sebelum' => null,
            'status_sesudah' => 'menunggu_tu',
        ]);

        return redirect()->route('permintaan.show', $permintaan)
            ->with('success', 'Permintaan warkah berhasil dibuat dengan nomor: ' . $permintaan->nomor_nota);
    }

    public function show(PermintaanWarkah $permintaan)
    {
        $permintaan->load(['pemohon', 'items', 'files.uploadedBy', 'logs.user', 'approvedByTu', 'processedByPhpt']);
        return view('permintaan.show', compact('permintaan'));
    }

    public function approveTU(Request $request, PermintaanWarkah $permintaan)
    {
        $request->validate([
            'aksi'     => 'required|in:setuju,tolak',
            'catatan'  => 'nullable|string|max:1000',
        ]);

        $statusLama  = $permintaan->status;
        $statusBaru  = $request->aksi === 'setuju' ? 'disetujui_tu' : 'ditolak_tu';

        $permintaan->update([
            'status'         => $statusBaru,
            'approved_by_tu' => Auth::id(),
            'approved_at_tu' => now(),
            'catatan_tu'     => $request->catatan,
        ]);

        AktivitasLog::create([
            'permintaan_id'  => $permintaan->id,
            'user_id'        => Auth::id(),
            'aksi'           => $request->aksi === 'setuju' ? 'Disetujui oleh Tata Usaha' : 'Ditolak oleh Tata Usaha',
            'catatan'        => $request->catatan,
            'status_sebelum' => $statusLama,
            'status_sesudah' => $statusBaru,
        ]);

        $msg = $request->aksi === 'setuju'
            ? 'Permintaan berhasil disetujui.'
            : 'Permintaan berhasil ditolak.';

        return redirect()->route('permintaan.show', $permintaan)->with('success', $msg);
    }

    public function uploadWarkah(Request $request, PermintaanWarkah $permintaan)
    {
        $request->validate([
            'files'   => 'required|array|min:1',
            'files.*' => 'required|file|mimes:pdf,zip,jpg,jpeg,png|max:51200',
            'catatan' => 'nullable|string|max:1000',
        ]);

        // Seksi hanya boleh mengunggah untuk permintaan yang ditujukan padanya.
        if (Auth::user()->isPetugasWarkah() && $permintaan->seksi_tujuan !== Auth::user()->role) {
            abort(403, 'Permintaan ini ditujukan ke seksi ' . $permintaan->seksi_tujuan_singkat . '.');
        }

        if ($permintaan->status === 'disetujui_tu') {
            $permintaan->update([
                'status'            => 'diproses_phpt',
                'processed_by_phpt' => Auth::id(),
                'processed_at_phpt' => now(),
            ]);
        }

        foreach ($request->file('files') as $file) {
            $path = $file->store('warkah/' . $permintaan->id, 'public');
            WarkahFile::create([
                'permintaan_id' => $permintaan->id,
                'nama_file'     => $file->getClientOriginalName(),
                'file_path'     => $path,
                'file_type'     => $file->getClientOriginalExtension(),
                'file_size'     => $file->getSize(),
                'uploaded_by'   => Auth::id(),
            ]);
        }

        $permintaan->update([
            'status'     => 'warkah_tersedia',
            'catatan_phpt' => $request->catatan,
        ]);

        AktivitasLog::create([
            'permintaan_id'  => $permintaan->id,
            'user_id'        => Auth::id(),
            'aksi'           => 'Warkah digital diupload oleh ' . $permintaan->seksi_tujuan_singkat
                                . ' (' . count($request->file('files')) . ' file)',
            'catatan'        => $request->catatan,
            'status_sebelum' => 'disetujui_tu',
            'status_sesudah' => 'warkah_tersedia',
        ]);

        return redirect()->route('permintaan.show', $permintaan)
            ->with('success', 'Warkah digital berhasil diupload.');
    }

    public function kembalikan(Request $request, PermintaanWarkah $permintaan)
    {
        $request->validate([
            'catatan_pengembalian' => 'required|string|max:1000',
        ]);

        $statusLama = $permintaan->status;
        $permintaan->update([
            'status'               => 'dikembalikan',
            'dikembalikan_at'      => now(),
            'catatan_pengembalian' => $request->catatan_pengembalian,
        ]);

        AktivitasLog::create([
            'permintaan_id'  => $permintaan->id,
            'user_id'        => Auth::id(),
            'aksi'           => 'Warkah dikembalikan oleh PPS',
            'catatan'        => $request->catatan_pengembalian,
            'status_sebelum' => $statusLama,
            'status_sesudah' => 'dikembalikan',
        ]);

        return redirect()->route('permintaan.show', $permintaan)
            ->with('success', 'Warkah berhasil dikembalikan.');
    }

    public function downloadFile(WarkahFile $file)
    {
        return Storage::disk('public')->download($file->file_path, $file->nama_file);
    }

    /**
     * Menampilkan berkas di dalam peramban tanpa mengunduh.
     * Hanya untuk jenis yang memang bisa dirender; ZIP tetap diarahkan ke unduhan.
     */
    public function previewFile(WarkahFile $file)
    {
        if (! $file->bisaDipratinjau()) {
            return redirect()->route('warkah.download', $file);
        }

        if (! Storage::disk('public')->exists($file->file_path)) {
            abort(404, 'Berkas tidak ditemukan.');
        }

        return response()->file(
            Storage::disk('public')->path($file->file_path),
            [
                'Content-Type'        => $file->mime_type,
                'Content-Disposition' => 'inline; filename="' . $file->nama_file . '"',
            ]
        );
    }
}
