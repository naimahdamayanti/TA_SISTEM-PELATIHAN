@extends('layouts.admin')

@section('title', 'Kelola Akun')

@section('content')

<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1">Kelola Akun Pengguna</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0 small">
                <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
                <li class="breadcrumb-item active">Kelola Akun</li>
            </ol>
        </nav>
    </div>
    <button type="button" class="btn btn-primary px-4" onclick="openModalTambah()">
        <i class="bi bi-plus-lg me-1"></i> Tambah Akun
    </button>
</div>

{{-- Alert --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif
@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2"></i>{{ $errors->first() }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card border-0 shadow-sm rounded-3">

    <div class="card-body border-bottom py-3 px-4">
        <form method="GET" action="{{ route('admin.akun.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" name="search" value="{{ request('search') }}"
                        class="form-control border-start-0 ps-0"
                        placeholder="Cari nama / email...">
                </div>
            </div>
            <div class="col-6 col-md-3">
                <select name="role" class="form-select">
                    <option value="">Semua Role</option>
                    <option value="admin"      @selected(request('role') === 'admin')>Admin</option>
                    <option value="instruktur" @selected(request('role') === 'instruktur')>Instruktur</option>
                    <option value="peserta"    @selected(request('role') === 'peserta')>Peserta</option>
                </select>
            </div>
            <div class="col-auto d-flex gap-2">
                <button type="submit" class="btn btn-primary px-4">Filter</button>
                <a href="{{ route('admin.akun.index') }}" class="btn btn-outline-secondary" title="Reset">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="ps-4 py-3" style="width:60px">ID</th>
                        <th class="py-3">Nama</th>
                        <th class="py-3">Username</th>
                        <th class="py-3">Email</th>
                        <th class="py-3">No HP</th>
                        <th class="py-3 text-center" style="width:110px">Role</th>
                        <th class="py-3 text-center pe-4" style="width:100px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    @php
                        $roleColor = match($user->role) {
                            'admin'      => ['bg' => '#fff0ee', 'text' => '#e84e3a'],
                            'instruktur' => ['bg' => '#e8f4fd', 'text' => '#3498db'],
                            'peserta'    => ['bg' => '#eafaf1', 'text' => '#2ecc71'],
                            default      => ['bg' => '#f5f5f5', 'text' => '#888'],
                        };
                        $isSelf = $user->id_user === Auth::user()->id_user;
                    @endphp
                    <tr>
                        <td class="ps-4 text-muted small">#{{ $user->id_user }}</td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="rounded-circle fw-bold d-flex align-items-center justify-content-center text-white"
                                     style="width:32px;height:32px;font-size:12px;flex-shrink:0;
                                            background:{{ $roleColor['text'] }}">
                                    {{ strtoupper(substr($user->nama, 0, 1)) }}
                                </div>
                                <span class="fw-semibold small">{{ $user->nama }}</span>
                            </div>
                        </td>
                        <td class="small text-muted">@<span>{{ $user->username }}</span></td>
                        <td class="small text-muted">{{ $user->email }}</td>
                        <td class="small">{{ $user->no_hp ?? '-' }}</td>
                        <td class="text-center">
                            <span class="badge rounded-pill px-3 py-1 fw-semibold"
                                  style="background:{{ $roleColor['bg'] }};color:{{ $roleColor['text'] }};font-size:11px">
                                {{ $user->role }}
                            </span>
                        </td>
                        <td class="text-center pe-4">
                            <div class="d-flex justify-content-center gap-1">
                                {{-- Edit --}}
                                <button type="button"
                                    class="btn btn-sm btn-outline-primary rounded-3"
                                    title="Edit Akun"
                                    onclick="openModalEdit({
                                        id:       {{ $user->id_user }},
                                        nama:     '{{ addslashes($user->nama) }}',
                                        username: '{{ addslashes($user->username) }}',
                                        email:    '{{ addslashes($user->email) }}',
                                        no_hp:    '{{ addslashes($user->no_hp ?? '') }}',
                                        role:     '{{ $user->role }}'
                                    })">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                {{-- Hapus (tidak bisa hapus diri sendiri) --}}
                                @if(!$isSelf)
                                <form action="{{ route('admin.akun.destroy', $user->id_user) }}"
                                      method="POST"
                                      onsubmit="return confirm('Hapus akun {{ addslashes($user->nama) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger rounded-3" title="Hapus">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                                @else
                                <button class="btn btn-sm btn-outline-secondary rounded-3" disabled title="Akun Anda sendiri">
                                    <i class="bi bi-trash"></i>
                                </button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            Tidak ada akun ditemukan.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    @if($users->hasPages())
    <div class="card-footer bg-white border-top py-3 px-4 d-flex justify-content-between align-items-center">
        <small class="text-muted">
            Menampilkan {{ $users->firstItem() }}–{{ $users->lastItem() }}
            dari {{ $users->total() }} akun
        </small>
        {{ $users->links() }}
    </div>
    @endif
</div>

<div class="modal fade" id="modalEdit" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Edit Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form id="formEdit" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama</label>
                        <input type="text" name="nama" id="edit_nama"
                            class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" id="edit_username"
                            class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" id="edit_email"
                            class="form-control rounded-3" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">No HP</label>
                        <input type="text" name="no_hp" id="edit_no_hp"
                            class="form-control rounded-3" placeholder="Opsional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role</label>
                        <select name="role" id="edit_role" class="form-select rounded-3" required>
                            <option value="admin">Admin</option>
                            <option value="instruktur">Instruktur</option>
                            <option value="peserta">Peserta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">
                            Password Baru
                            <span class="text-muted fw-normal">(opsional, kosongkan jika tidak diubah)</span>
                        </label>
                        <input type="password" name="password"
                            class="form-control rounded-3"
                            placeholder="Min. 8 karakter, huruf besar, angka">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control rounded-3"
                            placeholder="Ulangi password baru">
                    </div>
                    <div class="d-flex justify-content-end gap-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalTambah" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow rounded-4">
            <div class="modal-header border-0 px-4 pt-4 pb-2">
                <h5 class="modal-title fw-bold">Tambah Akun</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body px-4 pt-2 pb-0">
                <form action="{{ route('admin.akun.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Nama</label>
                        <input type="text" name="nama" class="form-control rounded-3"
                            value="{{ old('nama') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Username</label>
                        <input type="text" name="username" class="form-control rounded-3"
                            value="{{ old('username') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Email</label>
                        <input type="email" name="email" class="form-control rounded-3"
                            value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">No HP</label>
                        <input type="text" name="no_hp" class="form-control rounded-3"
                            value="{{ old('no_hp') }}" placeholder="Opsional">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Role</label>
                        <select name="role" class="form-select rounded-3" required>
                            <option value="">-- Pilih Role --</option>
                            <option value="admin"      {{ old('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                            <option value="instruktur" {{ old('role') === 'instruktur' ? 'selected' : '' }}>Instruktur</option>
                            <option value="peserta"    {{ old('role') === 'peserta' ? 'selected' : '' }}>Peserta</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Password</label>
                        <input type="password" name="password" class="form-control rounded-3"
                            placeholder="Min. 8 karakter, huruf besar, angka" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold small">Konfirmasi Password</label>
                        <input type="password" name="password_confirmation"
                            class="form-control rounded-3"
                            placeholder="Ulangi password" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2 pb-4">
                        <button type="button" class="btn btn-outline-secondary px-4 rounded-3"
                                data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary px-4 rounded-3 fw-semibold">
                            <i class="bi bi-plus-lg me-1"></i> Simpan Akun
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openModalTambah() {
        new bootstrap.Modal(document.getElementById('modalTambah')).show();
    }

    function openModalEdit(item) {
        const f = document.getElementById('formEdit');
        f.action = `/admin/akun/${item.id}`;

        document.getElementById('edit_nama').value     = item.nama     ?? '';
        document.getElementById('edit_username').value = item.username  ?? '';
        document.getElementById('edit_email').value    = item.email    ?? '';
        document.getElementById('edit_no_hp').value    = item.no_hp    ?? '';
        document.getElementById('edit_role').value     = item.role     ?? 'peserta';

        // Reset password field
        document.querySelector('#formEdit input[name="password"]').value              = '';
        document.querySelector('#formEdit input[name="password_confirmation"]').value = '';

        new bootstrap.Modal(document.getElementById('modalEdit')).show();
    }

    @if($errors->any())
        openModalTambah();
    @endif
</script>
@endpush

@endsection