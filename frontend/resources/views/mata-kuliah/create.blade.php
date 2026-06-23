@extends('layouts.app')
@section('title','Tambah Mata Kuliah')
@section('page-title','Tambah Mata Kuliah')
@section('content')
<div class="page-header">
    <div><h1>Tambah Mata Kuliah</h1></div>
    <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div class="card" style="max-width:520px">
    <form method="POST" action="{{ route('mata-kuliah.store') }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Kode Mata Kuliah *</label>
            <input type="text" name="kode" class="form-control" value="{{ old('kode') }}" placeholder="Contoh: TI2023" required>
        </div>
        <div class="form-group">
            <label class="form-label">Nama Mata Kuliah *</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama') }}" placeholder="Contoh: Algoritma & Pemrograman" required>
        </div>
        <div class="form-group">
            <label class="form-label">SKS *</label>
            <select name="sks" class="form-control" required>
                @for($i=1;$i<=6;$i++)
                    <option value="{{ $i }}" {{ old('sks',2)==$i?'selected':'' }}>{{ $i }} SKS</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Dosen Pengampu</label>
            <input type="text" name="dosen_pengampu" class="form-control" value="{{ old('dosen_pengampu') }}" placeholder="Nama dosen (opsional)">
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
