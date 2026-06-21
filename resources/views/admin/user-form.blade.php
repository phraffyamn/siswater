@extends('layouts.app')
@section('title', $user ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('page-title', $user ? 'Edit Pengguna' : 'Tambah Pengguna')
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.users') }}">Pengguna</a></li>
    <li class="breadcrumb-item active">{{ $user ? 'Edit' : 'Tambah' }}</li>
@endsection

@section('content')
<div class="row justify-content-center">
<div class="col-md-8">
<div class="card-siswa card">
    <div class="card-header">
        <i class="bi bi-person-{{ $user ? 'gear' : 'plus' }}-fill me-2"></i>
        {{ $user ? 'Edit Data Pengguna' : 'Tambah Pengguna Baru' }}
    </div>
    <div class="card-body">
        @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0 ps-3">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
        </div>
        @endif

        <form action="{{ $user ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST">
            @csrf
            @if($user) @method('PUT') @endif
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nama Lengkap <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                        value="{{ old('name', $user?->name) }}" required>
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Email <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                        value="{{ old('email', $user?->email) }}" required>
                    @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">NIP</label>
                    <input type="text" name="nip" class="form-control" maxlength="20"
                        value="{{ old('nip', $user?->nip) }}" placeholder="Nomor Induk Pegawai">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jabatan</label>
                    <input type="text" name="jabatan" class="form-control"
                        value="{{ old('jabatan', $user?->jabatan) }}" placeholder="cth: Kepala Seksi, Staf Penata...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Role / Seksi <span class="text-danger">*</span></label>
                    <select name="role" class="form-select @error('role') is-invalid @enderror" required>
                        @foreach(['admin'=>'Administrator','pps'=>'Pengendalian & Penanganan Sengketa (PPS)','phpt'=>'Penetapan Hak & Pendaftaran Tanah (PHPT)','tu'=>'Tata Usaha (TU)'] as $val => $lbl)
                        <option value="{{ $val }}" {{ old('role', $user?->role) === $val ? 'selected' : '' }}>{{ $lbl }}</option>
                        @endforeach
                    </select>
                    @error('role')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                @if($user)
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Status Akun</label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                            {{ old('is_active', $user?->is_active) ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Aktif</label>
                    </div>
                </div>
                @endif
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Password {{ $user ? '(kosongkan jika tidak diubah)' : '' }} <span class="text-danger">{{ !$user ? '*' : '' }}</span></label>
                    <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                        {{ !$user ? 'required' : '' }} minlength="6" placeholder="Min. 6 karakter">
                    @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Konfirmasi Password</label>
                    <input type="password" name="password_confirmation" class="form-control" minlength="6"
                        placeholder="Ulangi password">
                </div>
            </div>

            <hr>
            <div class="d-flex gap-2 justify-content-end">
                <a href="{{ route('admin.users') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-x me-1"></i>Batal
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-save-fill me-1"></i>{{ $user ? 'Simpan Perubahan' : 'Tambah Pengguna' }}
                </button>
            </div>
        </form>
    </div>
</div>
</div>
</div>
@endsection
