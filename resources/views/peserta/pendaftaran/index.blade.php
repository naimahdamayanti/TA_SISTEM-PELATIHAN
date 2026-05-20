@extends('layouts.peserta')

@section('title', 'Formulir Pendaftaran')
@section('page-title', 'Katalog Pelatihan')

@push('styles')
<style>
    /* ─── BREADCRUMB ─── */
    .breadcrumb-bar {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: #aaa;
        margin-bottom: 6px;
    }
    .breadcrumb-bar a { color: #aaa; text-decoration: none; }
    .breadcrumb-bar a:hover { color: var(--brand); }
    .breadcrumb-bar .sep { font-size: 11px; }
    .breadcrumb-bar .current { color: #555; font-weight: 500; }

    /* ─── PAGE HEADING ─── */
    .page-heading { margin-bottom: 20px; }
    .page-heading h5 { font-size: 17px; font-weight: 700; color: #1a1a2e; margin-bottom: 2px; }
    .page-heading p  { font-size: 12.5px; color: #aaa; margin: 0; }

    /* ─── INFO BANNER PELATIHAN ─── */
    .pelatihan-banner {
        background: var(--brand-light);
        border: 1px solid #fcd0c4;
        border-radius: 12px;
        padding: 14px 18px;
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .banner-icon {
        width: 40px; height: 40px;
        background: var(--brand);
        border-radius: 8px;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        font-size: 18px;
        flex-shrink: 0;
    }
    .banner-info { flex: 1; min-width: 0; }
    .banner-nama {
        font-size: 14px;
        font-weight: 700;
        color: #1a1a2e;
        margin-bottom: 3px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .banner-meta {
        font-size: 12px;
        color: #888;
        display: flex;
        align-items: center;
        gap: 14px;
        flex-wrap: wrap;
    }
    .banner-meta span { display: flex; align-items: center; gap: 4px; }
    .banner-sisa {
        margin-left: auto;
        flex-shrink: 0;
        text-align: right;
    }
    .banner-sisa .sisa-num {
        font-size: 18px;
        font-weight: 700;
        color: var(--brand);
        line-height: 1;
    }
    .banner-sisa .sisa-label {
        font-size: 10px;
        color: #aaa;
        white-space: nowrap;
    }

    /* ─── FORM WRAPPER ─── */
    .form-wrapper {
        background: #fff;
        border: 1px solid #eee;
        border-radius: 14px;
        overflow: hidden;
    }

    /* ─── SECTION HEADER ─── */
    .form-section-header {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 16px 24px;
        background: #fafafa;
        border-bottom: 1px solid #f0f0f0;
        font-size: 13px;
        font-weight: 700;
        color: var(--brand);
    }
    .form-section-header i { font-size: 15px; }

    /* ─── FORM BODY ─── */
    .form-body { padding: 22px 24px; }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    .form-row.single { grid-template-columns: 1fr; }
    @media (max-width: 640px) { .form-row { grid-template-columns: 1fr; } }

    .form-group { display: flex; flex-direction: column; gap: 5px; }
    .form-group label {
        font-size: 12px;
        font-weight: 600;
        color: #555;
    }
    .form-group label .required { color: var(--brand); margin-left: 2px; }

    .form-control-custom {
        width: 100%;
        padding: 9px 13px;
        border: 1.5px solid #e5e5e5;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        color: #333;
        background: #fff;
        outline: none;
        transition: border-color .15s, box-shadow .15s;
    }
    .form-control-custom::placeholder { color: #c4c4c4; }
    .form-control-custom:focus {
        border-color: var(--brand);
        box-shadow: 0 0 0 3px rgba(232,78,58,.08);
    }
    .form-control-custom.is-invalid { border-color: #ef4444; }
    textarea.form-control-custom {
        resize: vertical;
        min-height: 90px;
    }

    .invalid-feedback-custom {
        font-size: 11.5px;
        color: #ef4444;
        margin-top: 3px;
    }

    /* ─── SECTION DIVIDER ─── */
    .section-divider { border: none; border-top: 1px solid #f0f0f0; margin: 0; }

    /* ─── FOOTER TOMBOL ─── */
    .form-footer {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 18px 24px;
        background: #fafafa;
        border-top: 1px solid #f0f0f0;
    }

    .btn-kembali {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 10px 20px;
        background: #fff;
        border: 1.5px solid #e5e5e5;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 13.5px;
        font-weight: 600;
        color: #555;
        cursor: pointer;
        text-decoration: none;
        transition: background .15s, border-color .15s;
        white-space: nowrap;
    }
    .btn-kembali:hover { background: #f3f4f6; border-color: #d1d5db; color: #333; }

    .btn-kirim {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        padding: 11px 24px;
        background: var(--brand);
        border: none;
        border-radius: 8px;
        font-family: 'Outfit', sans-serif;
        font-size: 14px;
        font-weight: 700;
        color: #fff;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-kirim:hover { background: var(--brand-dark); }
</style>
@endpush

@section('content')

{{-- ── Breadcrumb ── --}}
<div class="breadcrumb-bar">
    <a href="{{ route('peserta.pelatihan.index') }}">Katalog Pelatihan</a>
    <span class="sep"><i class="bi bi-chevron-right"></i></span>
    <span class="current">Formulir Pendaftaran</span>
</div>

{{-- ── Heading ── --}}
<div class="page-heading">
    <h5>Formulir Pendaftaran</h5>
    <p>Isi data dengan lengkap dan benar</p>
</div>

{{-- ── Banner Info Pelatihan ── --}}
@php
    $kuota  = $pelatihan->kuota ?? 0;
    $terisi = $pelatihan->pendaftaran()->where('status', 'diterima')->count();
    $sisa   = max(0, $kuota - $terisi);
@endphp

<div class="pelatihan-banner">
    <div class="banner-icon">
        <i class="bi bi-journal-bookmark-fill"></i>
    </div>
    <div class="banner-info">
        <div class="banner-nama">{{ $pelatihan->nama_pelatihan }}</div>
        <div class="banner-meta">
            <span><i class="bi bi-upc-scan"></i> {{ $pelatihan->kode_pelatihan }}</span>
            <span><i class="bi bi-person-fill"></i>
                Instruktur: {{ $pelatihan->instruktur?->nama_lengkap ?? $pelatihan->instruktur?->nama ?? '-' }}
            </span>
            @if($pelatihan->tgl_mulai && $pelatihan->tgl_selesai)
            <span><i class="bi bi-calendar3"></i>
                Periode:
                {{ \Carbon\Carbon::parse($pelatihan->tgl_mulai)->translatedFormat('j M Y') }}
                –
                {{ \Carbon\Carbon::parse($pelatihan->tgl_selesai)->translatedFormat('j M Y') }}
            </span>
            @endif
        </div>
    </div>
    <div class="banner-sisa">
        <div class="sisa-num">{{ $sisa }}</div>
        <div class="sisa-label">Sisa Kuota</div>
        <div class="sisa-label">{{ $kuota }} tempat</div>
    </div>
</div>

{{-- ── Form Pendaftaran ── --}}
<form action="{{ route('peserta.pendaftaran.kirim', $pelatihan->id_pelatihan) }}"
      method="POST" id="formPendaftaran">
    @csrf

    <div class="form-wrapper">

        {{-- ══ SEKSI 1: Data Diri ══ --}}
        <div class="form-section-header">
            <i class="bi bi-person-lines-fill"></i> Data Diri Peserta
        </div>

        <div class="form-body">

            {{-- Nama Depan & Nama Belakang --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">Nama Depan <span class="required">*</span></label>
                    <input type="text" id="first_name" name="first_name"
                           class="form-control-custom @error('first_name') is-invalid @enderror"
                           placeholder="Nama depan"
                           value="{{ old('first_name', explode(' ', $peserta->nama_lengkap ?? $peserta->name ?? '')[0]) }}"
                           required>
                    @error('first_name')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="last_name">Nama Belakang</label>
                    <input type="text" id="last_name" name="last_name"
                           class="form-control-custom @error('last_name') is-invalid @enderror"
                           placeholder="Nama belakang"
                           value="{{ old('last_name', implode(' ', array_slice(explode(' ', $peserta->nama_lengkap ?? $peserta->name ?? ''), 1))) }}">
                    @error('last_name')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Email & No. HP --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" id="email" name="email"
                           class="form-control-custom @error('email') is-invalid @enderror"
                           placeholder="contoh@email.com"
                           value="{{ old('email', $peserta->email) }}"
                           required>
                    @error('email')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="no_hp">No. HP</label>
                    <input type="text" id="no_hp" name="no_hp"
                           class="form-control-custom @error('no_hp') is-invalid @enderror"
                           placeholder="08xxxxxxxxxx"
                           value="{{ old('no_hp', $peserta->no_hp ?? '') }}">
                    @error('no_hp')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Alamat --}}
            <div class="form-row single">
                <div class="form-group">
                    <label for="alamat">Alamat</label>
                    <textarea id="alamat" name="alamat"
                              class="form-control-custom @error('alamat') is-invalid @enderror"
                              placeholder="Alamat lengkap...">{{ old('alamat') }}</textarea>
                    @error('alamat')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
            </div>

        </div>{{-- /form-body --}}

        <hr class="section-divider">

        {{-- ══ SEKSI 2: Data Pekerjaan ══ --}}
        <div class="form-section-header">
            <i class="bi bi-briefcase-fill"></i> Data Pekerjaan &amp; Perusahaan
        </div>

        <div class="form-body">

            {{-- Pekerjaan & Nama Perusahaan --}}
            <div class="form-row">
                <div class="form-group">
                    <label for="pekerjaan">Pekerjaan / Jabatan</label>
                    <input type="text" id="pekerjaan" name="pekerjaan"
                           class="form-control-custom @error('pekerjaan') is-invalid @enderror"
                           placeholder="Staf K3 / Operator / dll."
                           value="{{ old('pekerjaan') }}">
                    @error('pekerjaan')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="perusahaan">Nama Perusahaan</label>
                    <input type="text" id="perusahaan" name="perusahaan"
                           class="form-control-custom @error('perusahaan') is-invalid @enderror"
                           placeholder="PT / CV / Instansi"
                           value="{{ old('perusahaan') }}">
                    @error('perusahaan')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- No. Telp Perusahaan --}}
            <div class="form-row" style="grid-template-columns: 1fr 1fr;">
                <div class="form-group">
                    <label for="tlp_perusahaan">No. Telp Perusahaan</label>
                    <input type="text" id="tlp_perusahaan" name="tlp_perusahaan"
                           class="form-control-custom @error('tlp_perusahaan') is-invalid @enderror"
                           placeholder="021xxxxxxx"
                           value="{{ old('tlp_perusahaan') }}">
                    @error('tlp_perusahaan')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
                {{-- kolom kosong untuk alignment 2 kolom --}}
                <div></div>
            </div>

        </div>{{-- /form-body --}}

        <hr class="section-divider">

        {{-- ══ SEKSI 3: Pesan Tambahan ══ --}}
        <div class="form-section-header">
            <i class="bi bi-chat-left-text-fill"></i> Pesan Tambahan
        </div>

        <div class="form-body">
            <div class="form-row single">
                <div class="form-group">
                    <label for="pesan">Pesan / Pertanyaan</label>
                    <textarea id="pesan" name="pesan"
                              class="form-control-custom @error('pesan') is-invalid @enderror"
                              placeholder="Tuliskan pertanyaan atau hal yang perlu disampaikan..."
                              style="min-height:110px">{{ old('pesan') }}</textarea>
                    @error('pesan')
                        <div class="invalid-feedback-custom">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>{{-- /form-body --}}

        {{-- ══ FOOTER TOMBOL ══ --}}
        <div class="form-footer">
            <a href="{{ route('peserta.pelatihan.index') }}" class="btn-kembali">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
            <button type="submit" class="btn-kirim">
                <i class="bi bi-send-check-fill"></i> Kirim Pendaftaran
            </button>
        </div>

    </div>{{-- /form-wrapper --}}

</form>

@endsection