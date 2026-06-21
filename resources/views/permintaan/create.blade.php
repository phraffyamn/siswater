@extends('layouts.app')
@section('title', 'Buat Permintaan Warkah')
@section('page-title', 'Buat Nota Dinas Permintaan Warkah')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('permintaan.index') }}">Permintaan</a></li>
    <li class="breadcrumb-item active">Buat Baru</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-xl-9">
<form action="{{ route('permintaan.store') }}" method="POST" id="form-permintaan">
@csrf
<div class="card-siswa card mb-3">
    <div class="card-header d-flex align-items-center gap-2">
        <i class="bi bi-file-earmark-text-fill"></i>
        <span>Nota Dinas Permintaan Warkah</span>
        <span class="ms-auto badge" style="background:var(--emas-muda);color:var(--emas)">PPS → TU → PHPT</span>
    </div>
    <div class="card-body">
        <!-- HEADER INFO -->
        <div class="row mb-3 p-3 rounded" style="background:#f0f4f8;border-left:4px solid var(--biru-tua)">
            <div class="col-md-6">
                <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">Dari</div>
                <div style="font-weight:700;color:#1a3a6b">{{ auth()->user()->name }}</div>
                <div style="font-size:.82rem;color:#6b7280">{{ auth()->user()->role_label }}</div>
                @if(auth()->user()->nip)<div style="font-size:.78rem;color:#6b7280">NIP: {{ auth()->user()->nip }}</div>@endif
            </div>
            <div class="col-md-6">
                <div style="font-size:.78rem;color:#6b7280;text-transform:uppercase;letter-spacing:.5px">Kepada</div>
                <div style="font-weight:700;color:#6b3a2a">Kepala Seksi Tata Usaha</div>
                <div style="font-size:.82rem;color:#6b7280">Kantor Pertanahan</div>
            </div>
        </div>

        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <div class="row g-3">
            <div class="col-md-8">
                <label class="form-label fw-semibold">Perihal <span class="text-danger">*</span></label>
                <input type="text" name="perihal" class="form-control @error('perihal') is-invalid @enderror"
                    placeholder="cth: Permohonan Warkah untuk Penanganan Sengketa..."
                    value="{{ old('perihal') }}" required>
                @error('perihal')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Target Selesai (Deadline)</label>
                <input type="date" name="deadline" class="form-control @error('deadline') is-invalid @enderror"
                    min="{{ now()->addDay()->format('Y-m-d') }}"
                    value="{{ old('deadline') }}">
                @error('deadline')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-12">
                <label class="form-label fw-semibold">Keterangan / Latar Belakang</label>
                <textarea name="keterangan" class="form-control" rows="3"
                    placeholder="Jelaskan alasan dan latar belakang permintaan warkah ini...">{{ old('keterangan') }}</textarea>
            </div>
        </div>
    </div>
</div>

<!-- DAFTAR WARKAH YANG DIMINTA -->
<div class="card-siswa card mb-3">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-list-check me-2"></i>Daftar Warkah yang Diminta</span>
        <button type="button" class="btn btn-sm" style="background:var(--emas);color:var(--biru-tua);font-weight:600" onclick="tambahWarkah()">
            <i class="bi bi-plus-circle me-1"></i>Tambah Warkah
        </button>
    </div>
    <div class="card-body">
        <div id="warkah-container">
            <!-- item pertama -->
            <div class="warkah-item border rounded p-3 mb-3 position-relative" style="background:#fafafa">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="badge" style="background:#dbeafe;color:#1a3a6b;font-weight:700">Warkah #1</span>
                    <button type="button" class="btn btn-sm btn-outline-danger btn-hapus" onclick="hapusWarkah(this)" style="display:none">
                        <i class="bi bi-trash3"></i>
                    </button>
                </div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Nama/Jenis Warkah <span class="text-danger">*</span></label>
                        <input type="text" name="nama_warkah[]" class="form-control form-control-sm" required
                            placeholder="cth: Sertifikat Hak Milik, Buku Tanah...">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Nomor Hak</label>
                        <input type="text" name="nomor_hak[]" class="form-control form-control-sm" placeholder="cth: SHM No. 001">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Nama Pemegang Hak</label>
                        <input type="text" name="nama_pemegang_hak[]" class="form-control form-control-sm" placeholder="Nama pemilik/pemegang hak">
                    </div>
                    <div class="col-md-5">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Lokasi/Alamat</label>
                        <input type="text" name="lokasi[]" class="form-control form-control-sm" placeholder="Desa, Kecamatan, Kabupaten">
                    </div>
                    <div class="col-md-7">
                        <label class="form-label" style="font-size:.82rem;font-weight:600">Keterangan Tambahan</label>
                        <input type="text" name="keterangan_item[]" class="form-control form-control-sm" placeholder="Catatan khusus...">
                    </div>
                </div>
            </div>
        </div>

        <div id="no-warkah" class="text-center py-3 text-muted" style="display:none">
            <i class="bi bi-list-check fs-2 d-block mb-1"></i>
            Klik "Tambah Warkah" untuk mulai mengisi daftar
        </div>
    </div>
</div>

<div class="d-flex gap-2 justify-content-end">
    <a href="{{ route('permintaan.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-x-circle me-1"></i>Batal
    </a>
    <button type="submit" class="btn btn-primary px-4">
        <i class="bi bi-send-fill me-1"></i>Kirim Permintaan
    </button>
</div>
</form>
</div>
</div>
@endsection

@push('scripts')
<script>
let counter = 1;

function tambahWarkah() {
    counter++;
    const tmpl = document.querySelector('.warkah-item').cloneNode(true);
    tmpl.querySelector('.badge').textContent = 'Warkah #' + counter;
    tmpl.querySelector('.btn-hapus').style.display = '';
    tmpl.querySelectorAll('input').forEach(i => i.value = '');
    document.getElementById('warkah-container').appendChild(tmpl);
    updateHapusButtons();
}

function hapusWarkah(btn) {
    btn.closest('.warkah-item').remove();
    updateNomor();
    updateHapusButtons();
}

function updateNomor() {
    document.querySelectorAll('.warkah-item').forEach((el, i) => {
        el.querySelector('.badge').textContent = 'Warkah #' + (i + 1);
    });
    counter = document.querySelectorAll('.warkah-item').length;
}

function updateHapusButtons() {
    const items = document.querySelectorAll('.warkah-item');
    items.forEach((el, i) => {
        el.querySelector('.btn-hapus').style.display = items.length > 1 ? '' : 'none';
    });
}
</script>
@endpush
