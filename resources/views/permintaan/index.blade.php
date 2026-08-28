@extends('layouts.app')
@section('title', 'Daftar Permintaan Warkah')
@section('page-title', 'Permintaan Warkah')
@section('breadcrumb')
    <li class="breadcrumb-item active">Permintaan Warkah</li>
@endsection

@section('content')
<div class="card-siswa card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="bi bi-folder2-open me-2"></i>
            @if(auth()->user()->isTU()) Kelola Permintaan Warkah
            @elseif(auth()->user()->isPetugasWarkah()) Antrian Upload Warkah {{ auth()->user()->role_singkat }}
            @else Permintaan Saya
            @endif
        </span>
        @if(auth()->user()->isPPS())
        <a href="{{ route('permintaan.create') }}" class="btn btn-sm" style="background:#b8860b;color:#fff;font-weight:600">
            <i class="bi bi-plus-circle-fill me-1"></i>Buat Permintaan Baru
        </a>
        @endif
    </div>

    <!-- FILTER BAR -->
    <div class="card-body pb-0" style="background:#f8fafc;border-bottom:1px solid #e5e7eb">
        <form action="{{ route('permintaan.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nomor nota atau perihal..."
                    value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach(['menunggu_tu'=>'Menunggu TU','disetujui_tu'=>'Disetujui TU','ditolak_tu'=>'Ditolak TU','diproses_phpt'=>'Diproses PHPT','warkah_tersedia'=>'Warkah Tersedia','dikembalikan'=>'Dikembalikan','selesai'=>'Selesai'] as $val=>$lbl)
                    <option value="{{ $val }}" {{ request('status')===$val?'selected':'' }}>{{ $lbl }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Filter
                </button>
            </div>
            @if(request()->hasAny(['search','status']))
            <div class="col-md-2">
                <a href="{{ route('permintaan.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
            </div>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;font-size:.78rem;text-transform:uppercase;letter-spacing:.5px">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th>Nomor Nota</th>
                        @if(!auth()->user()->isPPS())<th>Pemohon</th>@endif
                        <th>Perihal</th>
                        <th>Jml Warkah</th>
                        <th>Tanggal</th>
                        <th>Deadline</th>
                        <th>File</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaan as $i => $p)
                    <tr class="{{ $p->isOverdue() ? 'table-warning' : '' }}">
                        <td class="px-3 text-muted" style="font-size:.8rem">{{ $permintaan->firstItem() + $i }}</td>
                        <td><code style="color:#1a3a6b;font-size:.82rem">{{ $p->nomor_nota }}</code></td>
                        @if(!auth()->user()->isPPS())
                        <td>
                            <div style="font-size:.85rem;font-weight:600">{{ $p->pemohon->name }}</div>
                            <div style="font-size:.72rem;color:#6b7280">{{ $p->pemohon->jabatan ?? $p->pemohon->role_label }}</div>
                        </td>
                        @endif
                        <td class="text-truncate" style="max-width:180px;font-size:.85rem">{{ $p->perihal }}</td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark border">{{ $p->items_count ?? '—' }}</span>
                        </td>
                        <td style="font-size:.82rem">{{ $p->tanggal_permintaan->format('d/m/Y') }}</td>
                        <td>
                            @if($p->deadline)
                                <span class="{{ $p->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}" style="font-size:.82rem">
                                    {{ $p->deadline->format('d/m/Y') }}
                                    @if($p->isOverdue())<span class="overdue-badge ms-1">Terlambat</span>@endif
                                </span>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->files->count() > 0)
                                <span class="badge" style="background:#dcfce7;color:#166534">
                                    <i class="bi bi-file-earmark-fill me-1"></i>{{ $p->files->count() }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:.8rem">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('permintaan.show', $p) }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem" title="Detail">
                                    <i class="bi bi-eye-fill"></i>
                                </a>
                                @if(auth()->user()->isTU() && $p->status === 'menunggu_tu')
                                <a href="{{ route('permintaan.show', $p) }}#aksi-tu" class="btn btn-sm" style="background:#b8860b;color:#fff;font-size:.72rem" title="Proses">
                                    <i class="bi bi-clipboard-check-fill"></i>
                                </a>
                                @endif
                                @if(auth()->user()->isPetugasWarkah() && $p->seksi_tujuan === auth()->user()->role && in_array($p->status, ['disetujui_tu']))
                                <a href="{{ route('permintaan.show', $p) }}#upload-warkah" class="btn btn-sm btn-success" style="font-size:.72rem" title="Upload">
                                    <i class="bi bi-cloud-upload-fill"></i>
                                </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox display-5 d-block mb-2"></i>
                            <div>Tidak ada permintaan ditemukan</div>
                            @if(auth()->user()->isPPS())
                            <a href="{{ route('permintaan.create') }}" class="btn btn-sm btn-primary mt-2">
                                <i class="bi bi-plus me-1"></i>Buat Permintaan
                            </a>
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($permintaan->hasPages())
    <div class="card-body border-top">
        {{ $permintaan->links() }}
    </div>
    @endif
</div>
@endsection
