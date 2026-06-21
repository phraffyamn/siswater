@extends('layouts.app')
@section('title', 'Monitoring Warkah')
@section('page-title', 'Monitoring & Statistik')
@section('breadcrumb')
    <li class="breadcrumb-item active">Monitoring</li>
@endsection

@section('content')
<!-- STATS CARDS -->
<div class="row g-3 mb-4">
    @php
    $statusConfig = [
        'menunggu_tu'     => ['label'=>'Menunggu TU',      'icon'=>'bi-hourglass-split',     'color'=>'#b8860b', 'bg'=>'#fef3c7'],
        'disetujui_tu'    => ['label'=>'Disetujui TU',     'icon'=>'bi-check-circle',         'color'=>'#2563eb', 'bg'=>'#dbeafe'],
        'ditolak_tu'      => ['label'=>'Ditolak TU',       'icon'=>'bi-x-circle',             'color'=>'#dc2626', 'bg'=>'#fee2e2'],
        'diproses_phpt'   => ['label'=>'Diproses PHPT',    'icon'=>'bi-arrow-repeat',         'color'=>'#7c3aed', 'bg'=>'#ede9fe'],
        'warkah_tersedia' => ['label'=>'Warkah Tersedia',  'icon'=>'bi-file-earmark-check',   'color'=>'#166534', 'bg'=>'#dcfce7'],
        'dikembalikan'    => ['label'=>'Dikembalikan',     'icon'=>'bi-arrow-return-left',    'color'=>'#6b7280', 'bg'=>'#f3f4f6'],
        'selesai'         => ['label'=>'Selesai',          'icon'=>'bi-trophy',               'color'=>'#1a3a6b', 'bg'=>'#e0e7ff'],
    ];
    @endphp
    @foreach($statusConfig as $key => $cfg)
    <div class="col-md-3 col-6">
        <div class="stat-card" style="background:#fff;border-left:3px solid {{ $cfg['color'] }}">
            <div class="d-flex align-items-center gap-2">
                <div class="stat-icon" style="background:{{ $cfg['bg'] }};color:{{ $cfg['color'] }};width:40px;height:40px;font-size:1.1rem">
                    <i class="bi {{ $cfg['icon'] }}"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:{{ $cfg['color'] }};font-size:1.6rem">{{ $statsByStatus[$key] ?? 0 }}</div>
                    <div class="stat-label" style="font-size:.72rem">{{ $cfg['label'] }}</div>
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($overdueCount > 0)
    <div class="col-md-3 col-6">
        <div class="stat-card" style="background:#fff;border-left:3px solid #dc2626">
            <div class="d-flex align-items-center gap-2">
                <div class="stat-icon" style="background:#fee2e2;color:#dc2626;width:40px;height:40px;font-size:1.1rem">
                    <i class="bi bi-alarm-fill"></i>
                </div>
                <div>
                    <div class="stat-num" style="color:#dc2626;font-size:1.6rem">{{ $overdueCount }}</div>
                    <div class="stat-label overdue-badge" style="font-size:.72rem">Terlambat!</div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<!-- VISUAL PROGRESS BAR -->
@php $total = $statsByStatus->sum(); @endphp
@if($total > 0)
<div class="card-siswa card mb-4">
    <div class="card-header"><i class="bi bi-bar-chart-fill me-2"></i>Distribusi Status Permintaan</div>
    <div class="card-body">
        <div class="progress mb-2" style="height:24px;border-radius:12px;overflow:hidden">
            @foreach($statusConfig as $key => $cfg)
            @php $pct = round(($statsByStatus[$key] ?? 0) / $total * 100, 1); @endphp
            @if($pct > 0)
            <div class="progress-bar" role="progressbar" style="width:{{ $pct }}%;background:{{ $cfg['color'] }}" title="{{ $cfg['label'] }}: {{ $statsByStatus[$key] ?? 0 }} ({{ $pct }}%)">
                @if($pct > 8) {{ $pct }}% @endif
            </div>
            @endif
            @endforeach
        </div>
        <div class="d-flex flex-wrap gap-3 mt-2">
            @foreach($statusConfig as $key => $cfg)
            @if(($statsByStatus[$key] ?? 0) > 0)
            <div class="d-flex align-items-center gap-1" style="font-size:.78rem">
                <div style="width:10px;height:10px;border-radius:2px;background:{{ $cfg['color'] }}"></div>
                <span>{{ $cfg['label'] }}: <strong>{{ $statsByStatus[$key] }}</strong></span>
            </div>
            @endif
            @endforeach
        </div>
    </div>
</div>
@endif

<!-- FILTER & TABLE -->
<div class="card-siswa card">
    <div class="card-header d-flex align-items-center justify-content-between flex-wrap gap-2">
        <span><i class="bi bi-table me-2"></i>Detail Permintaan</span>
        <span class="badge" style="background:var(--emas-muda);color:var(--emas)">{{ $permintaan->total() }} total</span>
    </div>

    <div class="card-body pb-0" style="background:#f8fafc;border-bottom:1px solid #e5e7eb">
        <form action="{{ route('monitoring.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">Semua Status</option>
                    @foreach($statusConfig as $key => $cfg)
                    <option value="{{ $key }}" {{ request('status')===$key ? 'selected' : '' }}>{{ $cfg['label'] }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="bulan" class="form-select form-select-sm">
                    <option value="">Semua Bulan</option>
                    @foreach(range(1,12) as $m)
                    <option value="{{ $m }}" {{ request('bulan')==$m ? 'selected' : '' }}>
                        {{ \Carbon\Carbon::create()->month($m)->translatedFormat('F') }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="tahun" class="form-select form-select-sm">
                    <option value="">Semua Tahun</option>
                    @foreach(range(date('Y'), date('Y')-3) as $y)
                    <option value="{{ $y }}" {{ request('tahun')==$y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-sm btn-primary w-100"><i class="bi bi-funnel me-1"></i>Filter</button>
            </div>
            @if(request()->hasAny(['status','bulan','tahun']))
            <div class="col-md-2">
                <a href="{{ route('monitoring.index') }}" class="btn btn-sm btn-outline-secondary w-100">Reset</a>
            </div>
            @endif
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:.84rem">
                <thead style="background:#f8fafc;font-size:.75rem;text-transform:uppercase;letter-spacing:.5px">
                    <tr>
                        <th class="px-3 py-3">Nomor Nota</th>
                        @if(!auth()->user()->isPPS())<th>Pemohon</th>@endif
                        <th>Perihal</th>
                        <th>Dibuat</th>
                        <th>Deadline</th>
                        <th>Disetujui TU</th>
                        <th>Upload PHPT</th>
                        <th>Status</th>
                        <th>Durasi</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($permintaan as $p)
                    <tr class="{{ $p->isOverdue() ? 'table-warning' : '' }}">
                        <td class="px-3"><code style="color:#1a3a6b">{{ $p->nomor_nota }}</code></td>
                        @if(!auth()->user()->isPPS())
                        <td>
                            <div style="font-weight:600">{{ $p->pemohon->name }}</div>
                            <div style="font-size:.72rem;color:#6b7280">{{ $p->pemohon->role_label }}</div>
                        </td>
                        @endif
                        <td class="text-truncate" style="max-width:160px">{{ $p->perihal }}</td>
                        <td>{{ $p->tanggal_permintaan->format('d/m/Y') }}</td>
                        <td>
                            @if($p->deadline)
                            <span class="{{ $p->isOverdue() ? 'text-danger fw-bold' : '' }}">
                                {{ $p->deadline->format('d/m/Y') }}
                                @if($p->isOverdue())<br><span class="overdue-badge" style="font-size:.65rem">Terlambat</span>@endif
                            </span>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->approved_at_tu)
                            <div>{{ $p->approved_at_tu->format('d/m/Y') }}</div>
                            <div style="font-size:.72rem;color:#6b7280">{{ $p->approvedByTu?->name }}</div>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            @if($p->processed_at_phpt)
                            <div>{{ $p->processed_at_phpt->format('d/m/Y') }}</div>
                            <div style="font-size:.72rem;color:#6b7280">{{ $p->processedByPhpt?->name }}</div>
                            @else <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span>
                        </td>
                        <td>
                            @php
                            $end = in_array($p->status, ['warkah_tersedia','dikembalikan','selesai'])
                                ? ($p->processed_at_phpt ?? $p->updated_at)
                                : now();
                            $days = $p->tanggal_permintaan->diffInDays($end);
                            @endphp
                            <span class="{{ $days > 7 ? 'text-danger' : ($days > 3 ? 'text-warning' : 'text-success') }}" style="font-weight:600">
                                {{ $days }} hari
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('permintaan.show', $p) }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem">
                                <i class="bi bi-eye-fill"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-bar-chart display-5 d-block mb-2"></i>
                            Tidak ada data untuk ditampilkan
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($permintaan->hasPages())
    <div class="card-body border-top">{{ $permintaan->links() }}</div>
    @endif
</div>
@endsection
