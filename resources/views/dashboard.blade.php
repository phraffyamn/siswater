@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
<div class="row g-3 mb-4">
    @if($user->isAdmin())
        @php $colors = ['primary','warning','success','danger','info','secondary']; @endphp
        @foreach([
            ['label'=>'Total Permintaan','value'=>$stats['total_permintaan'],'icon'=>'bi-folder2-open','color'=>'#2563eb','bg'=>'#dbeafe'],
            ['label'=>'Menunggu TU','value'=>$stats['menunggu_tu'],'icon'=>'bi-hourglass-split','color'=>'#b8860b','bg'=>'#fef3c7'],
            ['label'=>'Sedang Diproses','value'=>$stats['diproses'],'icon'=>'bi-arrow-repeat','color'=>'#7c3aed','bg'=>'#ede9fe'],
            ['label'=>'Selesai','value'=>$stats['selesai'],'icon'=>'bi-check-circle-fill','color'=>'#166534','bg'=>'#dcfce7'],
            ['label'=>'Total Pengguna','value'=>$stats['total_users'],'icon'=>'bi-people-fill','color'=>'#1a3a6b','bg'=>'#e0e7ff'],
            ['label'=>'Melewati Deadline','value'=>$stats['overdue'],'icon'=>'bi-alarm-fill','color'=>'#dc2626','bg'=>'#fee2e2'],
        ] as $s)
        <div class="col-md-4 col-6">
            <div class="stat-card" style="background:#fff">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:{{$s['bg']}};color:{{$s['color']}}">
                        <i class="bi {{$s['icon']}}"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:{{$s['color']}}">{{ $s['value'] }}</div>
                        <div class="stat-label">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    @elseif($user->isPPS())
        @foreach([
            ['label'=>'Total Permintaan Saya','value'=>$stats['total_saya'],'icon'=>'bi-folder2-open','color'=>'#2563eb','bg'=>'#dbeafe'],
            ['label'=>'Sedang Diproses','value'=>$stats['menunggu'],'icon'=>'bi-hourglass-split','color'=>'#b8860b','bg'=>'#fef3c7'],
            ['label'=>'Warkah Tersedia','value'=>$stats['tersedia'],'icon'=>'bi-file-earmark-check','color'=>'#166534','bg'=>'#dcfce7'],
            ['label'=>'Melewati Deadline','value'=>$stats['overdue'],'icon'=>'bi-alarm-fill','color'=>'#dc2626','bg'=>'#fee2e2'],
        ] as $s)
        <div class="col-md-3 col-6">
            <div class="stat-card" style="background:#fff">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:{{$s['bg']}};color:{{$s['color']}}">
                        <i class="bi {{$s['icon']}}"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:{{$s['color']}}">{{ $s['value'] }}</div>
                        <div class="stat-label">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    @elseif($user->isTU())
        @foreach([
            ['label'=>'Menunggu Persetujuan','value'=>$stats['menunggu_persetujuan'],'icon'=>'bi-inbox-fill','color'=>'#b8860b','bg'=>'#fef3c7'],
            ['label'=>'Telah Disetujui','value'=>$stats['disetujui'],'icon'=>'bi-check-circle-fill','color'=>'#166534','bg'=>'#dcfce7'],
            ['label'=>'Ditolak','value'=>$stats['ditolak'],'icon'=>'bi-x-circle-fill','color'=>'#dc2626','bg'=>'#fee2e2'],
            ['label'=>'Total Bulan Ini','value'=>$stats['total_bulan_ini'],'icon'=>'bi-calendar-month','color'=>'#1a3a6b','bg'=>'#dbeafe'],
        ] as $s)
        <div class="col-md-3 col-6">
            <div class="stat-card" style="background:#fff">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:{{$s['bg']}};color:{{$s['color']}}">
                        <i class="bi {{$s['icon']}}"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:{{$s['color']}}">{{ $s['value'] }}</div>
                        <div class="stat-label">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

    @elseif($user->isPetugasWarkah())
        @foreach([
            ['label'=>'Antrian Upload','value'=>$stats['antrian_upload'],'icon'=>'bi-inbox-fill','color'=>'#b8860b','bg'=>'#fef3c7'],
            ['label'=>'Sedang Diproses','value'=>$stats['sedang_diproses'],'icon'=>'bi-arrow-repeat','color'=>'#7c3aed','bg'=>'#ede9fe'],
            ['label'=>'Selesai Diupload','value'=>$stats['selesai_diupload'],'icon'=>'bi-check-circle-fill','color'=>'#166534','bg'=>'#dcfce7'],
            ['label'=>'Selesai Bulan Ini','value'=>$stats['total_bulan_ini'],'icon'=>'bi-calendar-check','color'=>'#1a3a6b','bg'=>'#dbeafe'],
        ] as $s)
        <div class="col-md-3 col-6">
            <div class="stat-card" style="background:#fff">
                <div class="d-flex align-items-center gap-3">
                    <div class="stat-icon" style="background:{{$s['bg']}};color:{{$s['color']}}">
                        <i class="bi {{$s['icon']}}"></i>
                    </div>
                    <div>
                        <div class="stat-num" style="color:{{$s['color']}}">{{ $s['value'] }}</div>
                        <div class="stat-label">{{ $s['label'] }}</div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    @endif
</div>

<!-- QUICK ACTIONS -->
<div class="row g-3 mb-4">
    @if($user->isPPS())
    <div class="col-md-4">
        <a href="{{ route('permintaan.create') }}" class="card-siswa card text-decoration-none h-100" style="border-left: 4px solid #2563eb !important">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;background:#dbeafe;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#2563eb">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>
                <div>
                    <div class="fw-700" style="color:#1a3a6b;font-weight:700">Buat Nota Dinas</div>
                    <div class="text-muted" style="font-size:.8rem">Permintaan warkah baru</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    @endif

    @if($user->isTU())
    <div class="col-md-4">
        <a href="{{ route('permintaan.index', ['status'=>'menunggu_tu']) }}" class="card-siswa card text-decoration-none h-100" style="border-left: 4px solid #b8860b !important">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#b8860b">
                    <i class="bi bi-clipboard-check-fill"></i>
                </div>
                <div>
                    <div class="fw-700" style="color:#1a3a6b;font-weight:700">Berikan Persetujuan</div>
                    <div class="text-muted" style="font-size:.8rem">Verifikasi permintaan masuk</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    @endif

    @if($user->isPetugasWarkah())
    <div class="col-md-4">
        <a href="{{ route('permintaan.index', ['status'=>'disetujui_tu']) }}" class="card-siswa card text-decoration-none h-100" style="border-left: 4px solid #166534 !important">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;background:#dcfce7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#166534">
                    <i class="bi bi-cloud-upload-fill"></i>
                </div>
                <div>
                    <div class="fw-700" style="color:#1a3a6b;font-weight:700">Upload Warkah</div>
                    <div class="text-muted" style="font-size:.8rem">Unggah dokumen digital</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
    @endif

    <div class="col-md-4">
        <a href="{{ route('monitoring.index') }}" class="card-siswa card text-decoration-none h-100" style="border-left: 4px solid #6b3a2a !important">
            <div class="card-body d-flex align-items-center gap-3">
                <div style="width:50px;height:50px;background:#fef3c7;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:#6b3a2a">
                    <i class="bi bi-bar-chart-fill"></i>
                </div>
                <div>
                    <div class="fw-700" style="color:#1a3a6b;font-weight:700">Monitoring</div>
                    <div class="text-muted" style="font-size:.8rem">Pantau status & waktu</div>
                </div>
                <i class="bi bi-arrow-right ms-auto text-muted"></i>
            </div>
        </a>
    </div>
</div>

<!-- RECENT PERMINTAAN -->
<div class="card-siswa card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-clock-history me-2"></i>Permintaan Terbaru</span>
        <a href="{{ route('permintaan.index') }}" class="btn btn-sm" style="background:var(--emas);color:var(--biru-tua);font-weight:600">
            Lihat Semua <i class="bi bi-arrow-right"></i>
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">
                    <tr>
                        <th class="px-3 py-3">Nomor Nota</th>
                        <th>Pemohon</th>
                        <th>Perihal</th>
                        <th>Tanggal</th>
                        <th>Deadline</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPermintaan as $p)
                    <tr>
                        <td class="px-3"><code style="color:#1a3a6b">{{ $p->nomor_nota }}</code></td>
                        <td>
                            <div style="font-size:.85rem;font-weight:600">{{ $p->pemohon->name }}</div>
                            <div style="font-size:.72rem;color:#6b7280">{{ $p->pemohon->role_label }}</div>
                        </td>
                        <td class="text-truncate" style="max-width:200px">{{ $p->perihal }}</td>
                        <td style="font-size:.82rem">{{ $p->tanggal_permintaan->format('d/m/Y') }}</td>
                        <td>
                            @if($p->deadline)
                                <span style="font-size:.82rem" class="{{ $p->isOverdue() ? 'text-danger fw-bold' : 'text-muted' }}">
                                    {{ $p->deadline->format('d/m/Y') }}
                                    @if($p->isOverdue()) <span class="overdue-badge">!</span> @endif
                                </span>
                            @else
                                <span class="text-muted" style="font-size:.8rem">—</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge-status status-{{ $p->status }}">{{ $p->status_label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('permintaan.show', $p) }}" class="btn btn-sm btn-outline-primary" style="font-size:.75rem">
                                Detail
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">
                            <i class="bi bi-inbox display-6 d-block mb-2"></i>
                            Belum ada permintaan warkah
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
