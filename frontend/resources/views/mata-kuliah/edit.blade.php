@extends('layouts.app')
@section('title','Edit Mata Kuliah')
@section('page-title','Edit Mata Kuliah')
@section('content')
<div class="page-header">
    <div><h1>Edit Mata Kuliah</h1><p>{{ $mataKuliah->kode }}</p></div>
    <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div class="card" style="max-width:520px">
    <form method="POST" action="{{ route('mata-kuliah.update', $mataKuliah->id) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Kode *</label>
            <input type="text" name="kode" class="form-control" value="{{ old('kode',$mataKuliah->kode) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">Nama *</label>
            <input type="text" name="nama" class="form-control" value="{{ old('nama',$mataKuliah->nama) }}" required>
        </div>
        <div class="form-group">
            <label class="form-label">SKS *</label>
            <select name="sks" class="form-control" required>
                @for($i=1;$i<=6;$i++)
                    <option value="{{ $i }}" {{ old('sks',$mataKuliah->sks)==$i?'selected':'' }}>{{ $i }} SKS</option>
                @endfor
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Dosen Pengampu</label>
            <input type="text" name="dosen_pengampu" class="form-control" value="{{ old('dosen_pengampu',$mataKuliah->dosen_pengampu) }}">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.85rem">
                <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif',$mataKuliah->is_aktif)?'checked':'' }}> Mata kuliah aktif
            </label>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
