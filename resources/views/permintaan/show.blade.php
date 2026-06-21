@extends('layouts.app')
@section('title', 'Detail Permintaan')
@section('page-title', 'Detail Permintaan Warkah')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('permintaan.index') }}">Permintaan</a></li>
    <li class="breadcrumb-item active">{{ $permintaan->nomor_nota }}</li>
@endsection

@section('content')
@php $user = auth()->user(); @endphp

<div class="row g-3">
<!-- LEFT: DETAIL -->
<div class="col-lg-8">

    <!-- HEADER NOTA DINAS -->
    <div class="card-siswa card mb-3">
        <div class="card-body">
            <div class="d-flex align-items-start justify-content-between flex-wrap gap-2">
                <div>
                    <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">Nomor Nota Dinas</div>
                    <h4 style="color:#1a3a6b;font-weight:800;font-family:monospace">{{ $permintaan->nomor_nota }}</h4>
                </div>
                <div class="d-flex flex-column align-items-end gap-2">
                    <span class="badge-status status-{{ $permintaan->status }}" style="font-size:.85rem;padding:.5em 1em">
                        {{ $permintaan->status_label }}
                    </span>
                    @if($permintaan->isOverdue())
                    <span class="overdue-badge">MELEWATI DEADLINE</span>
                    @endif
                </div>
            </div>

            <hr>

            <div class="row g-3">
                <div class="col-md-6">
                    <div class="p-3 rounded" style="background:#f0f4f8">
                        <div style="font-size:.72rem;color:#6b7280;text-transform:uppercase;font-weight:600">Dari (Pemohon)</div>
                        <div class="fw-bold" style="color:#1a3a6b">{{ $permintaan->pemohon->name }}</div>
                        <div style="font-size:.82rem;color:#6b7280">{{ $permintaan->pemohon->role_label }}</div>
                        @if($permintaan->pemohon->nip)
                        <div style="font-size:.78rem;color:#6b7280">NIP: {{ $permintaan->pemohon->nip }}</div>
                        @endif
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded" style="background:#f0f4f8">
                        <div style="font-size:.72rem;color:#6b7280;text-transform:uppercase;font-weight:600">Tanggal Dibuat</div>
                        <div class="fw-bold">{{ $permintaan->tanggal_permintaan->format('d/m/Y') }}</div>
                        <div style="font-size:.78rem;color:#6b7280">{{ $permintaan->tanggal_permintaan->format('H:i') }} WIB</div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="p-3 rounded {{ $permintaan->isOverdue() ? '' : '' }}" style="background:{{ $permintaan->isOverdue() ? '#fee2e2' : '#f0f4f8' }}">
                        <div style="font-size:.72rem;color:#6b7280;text-transform:uppercase;font-weight:600">Deadline</div>
                        @if($permintaan->deadline)
                        <div class="fw-bold {{ $permintaan->isOverdue() ? 'text-danger' : '' }}">
                            {{ $permintaan->deadline->format('d/m/Y') }}
                        </div>
                        <div style="font-size:.78rem" class="{{ $permintaan->isOverdue() ? 'text-danger' : 'text-muted' }}">
                            {{ $permintaan->isOverdue() ? 'Telah melewati batas' : $permintaan->deadline->diffForHumans() }}
                        </div>
                        @else
                        <div class="text-muted">Tidak ditentukan</div>
                        @endif
                    </div>
                </div>
                <div class="col-12">
                    <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:.25rem">Perihal</div>
                    <div class="fw-semibold">{{ $permintaan->perihal }}</div>
                </div>
                @if($permintaan->keterangan)
                <div class="col-12">
                    <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;font-weight:600;margin-bottom:.25rem">Keterangan</div>
                    <div style="font-size:.9rem;color:#374151">{{ $permintaan->keterangan }}</div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- DAFTAR WARKAH DIMINTA -->
    <div class="card-siswa card mb-3">
        <div class="card-header">
            <i class="bi bi-list-check me-2"></i>Daftar Warkah yang Diminta
            <span class="badge ms-2" style="background:var(--emas);color:var(--biru-tua)">{{ $permintaan->items->count() }} item</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead style="background:#f8fafc;font-size:.78rem;text-transform:uppercase">
                        <tr>
                            <th class="px-3 py-2">#</th>
                            <th>Nama/Jenis Warkah</th>
                            <th>Nomor Hak</th>
                            <th>Pemegang Hak</th>
                            <th>Lokasi</th>
                            <th>File Tersedia</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($permintaan->items as $i => $item)
                        <tr>
                            <td class="px-3 text-muted">{{ $i + 1 }}</td>
                            <td class="fw-semibold">{{ $item->nama_warkah }}</td>
                            <td><code style="font-size:.82rem">{{ $item->nomor_hak ?? '—' }}</code></td>
                            <td>{{ $item->nama_pemegang_hak ?? '—' }}</td>
                            <td style="font-size:.82rem">{{ $item->lokasi ?? '—' }}</td>
                            <td>
                                @if($item->files->count() > 0)
                                <span class="badge" style="background:#dcfce7;color:#166534">
                                    <i class="bi bi-check-circle-fill me-1"></i>{{ $item->files->count() }} file
                                </span>
                                @else
                                <span class="badge" style="background:#f3f4f6;color:#6b7280">Belum ada</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- FILE WARKAH DIGITAL -->
    @if($permintaan->files->count() > 0)
    <div class="card-siswa card mb-3">
        <div class="card-header" style="background:#166534">
            <i class="bi bi-file-earmark-fill me-2"></i>File Warkah Digital
            <span class="badge ms-2 bg-white text-success">{{ $permintaan->files->count() }} file</span>
        </div>
        <div class="card-body">
            <div class="row g-2">
                @foreach($permintaan->files as $file)
                <div class="col-md-6">
                    <div class="d-flex align-items-center gap-2 p-2 rounded border" style="background:#f8fafc">
                        <div style="width:40px;height:40px;background:{{ $file->file_type==='pdf' ? '#fee2e2' : '#dbeafe' }};border-radius:8px;display:flex;align-items:center;justify-content:center;color:{{ $file->file_type==='pdf' ? '#dc2626' : '#2563eb' }};font-size:1.2rem">
                            <i class="bi bi-file-earmark-{{ $file->file_type==='pdf' ? 'pdf' : 'zip' }}-fill"></i>
                        </div>
                        <div class="flex-fill" style="min-width:0">
                            <div class="text-truncate fw-semibold" style="font-size:.83rem">{{ $file->nama_file }}</div>
                            <div style="font-size:.72rem;color:#6b7280">
                                {{ strtoupper($file->file_type) }} • {{ $file->file_size_human }}
                                • {{ $file->uploadedBy->name }}
                            </div>
                        </div>
                        <a href="{{ route('warkah.download', $file) }}" class="btn btn-sm btn-outline-primary" title="Unduh">
                            <i class="bi bi-download"></i>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @if($permintaan->catatan_phpt)
            <div class="mt-2 p-2 rounded" style="background:#dcfce7;font-size:.83rem;color:#166534">
                <i class="bi bi-chat-text-fill me-1"></i><strong>Catatan PHPT:</strong> {{ $permintaan->catatan_phpt }}
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- AKSI PENGEMBALIAN (PPS) -->
    @if($user->isPPS() && $permintaan->pemohon_id === $user->id && $permintaan->status === 'warkah_tersedia')
    <div class="card-siswa card mb-3" id="pengembalian" style="border-left: 4px solid #6b7280 !important">
        <div class="card-header" style="background:#374151">
            <i class="bi bi-arrow-return-left me-2"></i>Kembalikan Warkah
        </div>
        <div class="card-body">
            <p class="text-muted" style="font-size:.88rem">Setelah selesai menggunakan warkah, kembalikan dengan mengisi catatan pengembalian di bawah ini.</p>
            <form action="{{ route('permintaan.kembalikan', $permintaan) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Pengembalian <span class="text-danger">*</span></label>
                    <textarea name="catatan_pengembalian" class="form-control" rows="2" required
                        placeholder="cth: Warkah telah digunakan untuk keperluan... Dikembalikan dalam keadaan lengkap."></textarea>
                </div>
                <button type="submit" class="btn btn-secondary" onclick="return confirm('Konfirmasi pengembalian warkah?')">
                    <i class="bi bi-arrow-return-left me-1"></i>Kembalikan Warkah
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- UPLOAD WARKAH (PHPT) -->
    @if($user->isPHPT() && in_array($permintaan->status, ['disetujui_tu', 'diproses_phpt']))
    <div class="card-siswa card mb-3" id="upload-warkah">
        <div class="card-header" style="background:#166534">
            <i class="bi bi-cloud-upload-fill me-2"></i>Upload Warkah Digital
        </div>
        <div class="card-body">
            <form action="{{ route('permintaan.upload', $permintaan) }}" method="POST" enctype="multipart/form-data" id="upload-form">
                @csrf
                <div class="upload-area mb-3" onclick="document.getElementById('file-input').click()">
                    <i class="bi bi-cloud-arrow-up-fill" style="font-size:2.5rem;color:#2563eb"></i>
                    <div class="fw-bold mt-2" style="color:#1a3a6b">Klik atau drag & drop file di sini</div>
                    <div class="text-muted" style="font-size:.82rem">Format: PDF atau ZIP • Maksimal 50 MB per file • Bisa pilih banyak file sekaligus</div>
                </div>
                <input type="file" id="file-input" name="files[]" multiple accept=".pdf,.zip" style="display:none" onchange="previewFiles(this)">

                <div id="file-preview" class="mb-3"></div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan Upload</label>
                    <textarea name="catatan" class="form-control" rows="2" placeholder="Keterangan tambahan mengenai warkah yang diupload..."></textarea>
                </div>

                <button type="submit" class="btn btn-success" id="btn-upload" disabled>
                    <i class="bi bi-cloud-upload-fill me-1"></i>Upload Warkah
                </button>
            </form>
        </div>
    </div>
    @endif

    <!-- AKSI TU -->
    @if($user->isTU() && $permintaan->status === 'menunggu_tu')
    <div class="card-siswa card mb-3" id="aksi-tu">
        <div class="card-header" style="background:#b8860b">
            <i class="bi bi-clipboard-check-fill me-2"></i>Tindak Lanjut Tata Usaha
        </div>
        <div class="card-body">
            <form action="{{ route('permintaan.approve.tu', $permintaan) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label fw-semibold">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="2"
                        placeholder="Catatan persetujuan atau alasan penolakan..."></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" name="aksi" value="setuju" class="btn btn-success flex-fill"
                        onclick="return confirm('Setujui permintaan ini?')">
                        <i class="bi bi-check-circle-fill me-1"></i>Setujui
                    </button>
                    <button type="submit" name="aksi" value="tolak" class="btn btn-danger flex-fill"
                        onclick="return confirm('Tolak permintaan ini?')">
                        <i class="bi bi-x-circle-fill me-1"></i>Tolak
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

</div>

<!-- RIGHT: PROGRESS & LOG -->
<div class="col-lg-4">

    <!-- STATUS PROGRESS -->
    <div class="card-siswa card mb-3">
        <div class="card-header"><i class="bi bi-diagram-3-fill me-2"></i>Alur Proses</div>
        <div class="card-body">
            @php
            $steps = [
                ['status'=>['menunggu_tu','disetujui_tu','ditolak_tu','diproses_phpt','warkah_tersedia','dikembalikan','selesai'],'label'=>'Dibuat (PPS)','icon'=>'bi-person-fill','color'=>'#2563eb'],
                ['status'=>['disetujui_tu','diproses_phpt','warkah_tersedia','dikembalikan','selesai'],'label'=>'Disetujui TU','icon'=>'bi-clipboard-check-fill','color'=>'#b8860b'],
                ['status'=>['diproses_phpt','warkah_tersedia','dikembalikan','selesai'],'label'=>'Diproses PHPT','icon'=>'bi-archive-fill','color'=>'#7c3aed'],
                ['status'=>['warkah_tersedia','dikembalikan','selesai'],'label'=>'Warkah Tersedia','icon'=>'bi-file-earmark-check-fill','color'=>'#166534'],
                ['status'=>['dikembalikan','selesai'],'label'=>'Dikembalikan','icon'=>'bi-arrow-return-left','color'=>'#6b7280'],
            ];
            if($permintaan->status === 'ditolak_tu') {
                $steps[1] = ['status'=>['ditolak_tu'],'label'=>'Ditolak TU','icon'=>'bi-x-circle-fill','color'=>'#dc2626'];
            }
            @endphp
            <div class="timeline">
                @foreach($steps as $step)
                @php $active = in_array($permintaan->status, $step['status']); @endphp
                <div class="timeline-item">
                    <div class="timeline-dot {{ $active ? 'active' : '' }}" style="{{ $active ? 'background:'.$step['color'].';border-color:'.$step['color'] : '' }}"></div>
                    <div class="ms-1">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi {{ $step['icon'] }}" style="color:{{ $active ? $step['color'] : '#d1d5db' }};font-size:.9rem"></i>
                            <span style="font-size:.85rem;font-weight:{{ $active ? '700' : '400' }};color:{{ $active ? $step['color'] : '#9ca3af' }}">
                                {{ $step['label'] }}
                            </span>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            @if($permintaan->approved_at_tu)
            <div class="mt-3 p-2 rounded" style="background:#fef3c7;font-size:.78rem">
                <i class="bi bi-clipboard-check-fill me-1" style="color:#b8860b"></i>
                <strong>TU:</strong> {{ $permintaan->approvedByTu?->name }} —
                {{ $permintaan->approved_at_tu->format('d/m/Y H:i') }}
                @if($permintaan->catatan_tu)
                <div class="mt-1">{{ $permintaan->catatan_tu }}</div>
                @endif
            </div>
            @endif

            @if($permintaan->processed_at_phpt)
            <div class="mt-2 p-2 rounded" style="background:#ede9fe;font-size:.78rem">
                <i class="bi bi-archive-fill me-1" style="color:#7c3aed"></i>
                <strong>PHPT:</strong> {{ $permintaan->processedByPhpt?->name }} —
                {{ $permintaan->processed_at_phpt->format('d/m/Y H:i') }}
            </div>
            @endif
        </div>
    </div>

    <!-- AKTIVITAS LOG -->
    <div class="card-siswa card">
        <div class="card-header"><i class="bi bi-clock-history me-2"></i>Riwayat Aktivitas</div>
        <div class="card-body p-0">
            @forelse($permintaan->logs as $log)
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-start gap-2">
                    <div style="width:32px;height:32px;background:#dbeafe;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.8rem;font-weight:700;color:#1a3a6b;flex-shrink:0">
                        {{ strtoupper(substr($log->user->name, 0, 1)) }}
                    </div>
                    <div class="flex-fill">
                        <div style="font-size:.82rem;font-weight:600">{{ $log->aksi }}</div>
                        <div style="font-size:.75rem;color:#6b7280">
                            {{ $log->user->name }} • {{ $log->created_at->diffForHumans() }}
                        </div>
                        @if($log->catatan)
                        <div class="mt-1 p-1 rounded" style="background:#f3f4f6;font-size:.75rem;color:#374151">
                            {{ $log->catatan }}
                        </div>
                        @endif
                        @if($log->status_sebelum || $log->status_sesudah)
                        <div class="mt-1" style="font-size:.72rem;color:#6b7280">
                            @if($log->status_sebelum)<span class="badge" style="background:#f3f4f6;color:#374151">{{ $log->status_sebelum }}</span>@endif
                            @if($log->status_sebelum && $log->status_sesudah)<i class="bi bi-arrow-right mx-1"></i>@endif
                            @if($log->status_sesudah)<span class="badge" style="background:#dbeafe;color:#1a3a6b">{{ $log->status_sesudah }}</span>@endif
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @empty
            <div class="p-3 text-center text-muted" style="font-size:.85rem">Belum ada aktivitas</div>
            @endforelse
        </div>
    </div>

</div>
</div>
@endsection

@push('scripts')
<script>
function previewFiles(input) {
    const preview = document.getElementById('file-preview');
    const btn = document.getElementById('btn-upload');
    preview.innerHTML = '';
    if (input.files.length === 0) { btn.disabled = true; return; }
    const icons = { pdf: '🔴 PDF', zip: '🔵 ZIP' };
    Array.from(input.files).forEach(f => {
        const ext = f.name.split('.').pop().toLowerCase();
        const size = f.size > 1048576 ? (f.size/1048576).toFixed(2)+' MB' : (f.size/1024).toFixed(1)+' KB';
        const div = document.createElement('div');
        div.className = 'd-flex align-items-center gap-2 p-2 mb-1 rounded border';
        div.style.background = '#f8fafc';
        div.innerHTML = `<span>${icons[ext]||'📄'}</span><span class="flex-fill" style="font-size:.83rem;font-weight:600">${f.name}</span><span class="text-muted" style="font-size:.75rem">${size}</span>`;
        preview.appendChild(div);
    });
    btn.disabled = false;
}

// Drag and drop
const uploadArea = document.querySelector('.upload-area');
if (uploadArea) {
    uploadArea.addEventListener('dragover', e => { e.preventDefault(); uploadArea.style.borderColor='#b8860b'; });
    uploadArea.addEventListener('dragleave', () => { uploadArea.style.borderColor=''; });
    uploadArea.addEventListener('drop', e => {
        e.preventDefault();
        uploadArea.style.borderColor = '';
        const dt = e.dataTransfer;
        document.getElementById('file-input').files = dt.files;
        previewFiles(document.getElementById('file-input'));
    });
}
</script>
@endpush
