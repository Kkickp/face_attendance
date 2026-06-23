@extends('layouts.app')
@section('title','Edit Mahasiswa')
@section('page-title','Edit Mahasiswa')
@section('content')
<div class="page-header">
    <div><h1>Edit Mahasiswa</h1><p>{{ $mahasiswa->nim }}</p></div>
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div class="card" style="max-width:480px">
    <form method="POST" action="{{ route('mahasiswa.update', $mahasiswa->nim) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">NIM</label>
            <input type="text" class="form-control" value="{{ $mahasiswa->nim }}" disabled>
        </div>
        <div class="form-group">
            <label class="form-label">Nama Lengkap <span style="color:var(--danger-light)">*</span></label>
            <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap', $mahasiswa->nama_lengkap) }}" required>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
