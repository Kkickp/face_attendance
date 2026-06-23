@extends('layouts.app')
@section('title','Edit Jadwal Kelas')
@section('page-title','Edit Jadwal Kelas')
@section('content')
<div class="page-header">
    <div><h1>Edit Jadwal Kelas</h1></div>
    <a href="{{ route('kelas.index') }}" class="btn btn-secondary">← Kembali</a>
</div>
<div class="card" style="max-width:520px">
    <form method="POST" action="{{ route('kelas.update', $kelas->id) }}">
        @csrf @method('PUT')
        <div class="form-group">
            <label class="form-label">Mata Kuliah *</label>
            <select name="mata_kuliah_id" class="form-control" required>
                @foreach($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" {{ old('mata_kuliah_id',$kelas->mata_kuliah_id)==$mk->id?'selected':'' }}>{{ $mk->kode }} — {{ $mk->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Hari *</label>
            <select name="hari" class="form-control" required>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'] as $h)
                    <option value="{{ $h }}" {{ old('hari',$kelas->hari)==$h?'selected':'' }}>{{ $h }}</option>
                @endforeach
            </select>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:0.75rem">
            <div class="form-group">
                <label class="form-label">Jam Mulai *</label>
                <input type="time" name="jam_mulai" class="form-control" value="{{ old('jam_mulai', substr($kelas->jam_mulai,0,5)) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Jam Selesai *</label>
                <input type="time" name="jam_selesai" class="form-control" value="{{ old('jam_selesai', substr($kelas->jam_selesai,0,5)) }}" required>
            </div>
        </div>
        <div class="form-group">
            <label class="form-label">Ruangan</label>
            <input type="text" name="ruangan" class="form-control" value="{{ old('ruangan',$kelas->ruangan) }}">
        </div>
        <div class="form-group">
            <label style="display:flex;align-items:center;gap:0.5rem;cursor:pointer;font-size:0.85rem">
                <input type="checkbox" name="is_aktif" value="1" {{ old('is_aktif',$kelas->is_aktif)?'checked':'' }}> Kelas aktif
            </label>
        </div>
        <div style="display:flex;gap:0.75rem;margin-top:1rem">
            <button type="submit" class="btn btn-primary">💾 Simpan</button>
            <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
