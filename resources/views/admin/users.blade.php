@extends('layouts.app')
@section('title', 'Kelola Pengguna')
@section('page-title', 'Kelola Pengguna')
@section('breadcrumb')
    <li class="breadcrumb-item active">Pengguna</li>
@endsection

@section('content')
<div class="card-siswa card">
    <div class="card-header d-flex align-items-center justify-content-between">
        <span><i class="bi bi-people-fill me-2"></i>Daftar Pengguna Sistem</span>
        <a href="{{ route('admin.users.create') }}" class="btn btn-sm" style="background:#b8860b;color:#fff;font-weight:600">
            <i class="bi bi-person-plus-fill me-1"></i>Tambah Pengguna
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead style="background:#f8fafc;font-size:.8rem;text-transform:uppercase;letter-spacing:.5px">
                    <tr>
                        <th class="px-3 py-3">#</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>NIP</th>
                        <th>Jabatan</th>
                        <th>Role/Seksi</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $i => $u)
                    <tr>
                        <td class="px-3 text-muted">{{ $users->firstItem() + $i }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div style="width:34px;height:34px;background:var(--biru-muda);border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:700;color:#1a3a6b;font-size:.85rem">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div class="fw-semibold">{{ $u->name }}</div>
                            </div>
                        </td>
                        <td style="font-size:.85rem">{{ $u->email }}</td>
                        <td style="font-size:.82rem">{{ $u->nip ?? '—' }}</td>
                        <td style="font-size:.82rem">{{ $u->jabatan ?? '—' }}</td>
                        <td>
                            <span class="badge text-white role-{{ $u->role }}">{{ $u->role_label }}</span>
                        </td>
                        <td>
                            <span class="badge" style="background:{{ $u->is_active ? '#dcfce7' : '#fee2e2' }};color:{{ $u->is_active ? '#166534' : '#dc2626' }}">
                                {{ $u->is_active ? 'Aktif' : 'Nonaktif' }}
                            </span>
                        </td>
                        <td>
                            <div class="d-flex gap-1">
                                <a href="{{ route('admin.users.edit', $u) }}" class="btn btn-sm btn-outline-primary" style="font-size:.72rem">
                                    <i class="bi bi-pencil-fill"></i>
                                </a>
                                @if($u->id !== auth()->id())
                                <form action="{{ route('admin.users.toggle', $u) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="btn btn-sm {{ $u->is_active ? 'btn-outline-warning' : 'btn-outline-success' }}" style="font-size:.72rem"
                                        title="{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}"
                                        onclick="return confirm('{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }} pengguna ini?')">
                                        <i class="bi bi-{{ $u->is_active ? 'person-dash' : 'person-check' }}-fill"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-muted">Belum ada pengguna</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($users->hasPages())
    <div class="card-body border-top">{{ $users->links() }}</div>
    @endif
</div>
@endsection
