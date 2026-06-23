@extends('layouts.app')
@section('title','Jadwal Kelas')
@section('page-title','Kelas & Jadwal')
@section('page-subtitle','Daftar jadwal kelas per mata kuliah')
@section('content')
<div class="page-header">
    <div><h1>Jadwal Kelas</h1></div>
    <a href="{{ route('kelas.create') }}" class="btn btn-primary">➕ Tambah Jadwal</a>
</div>
<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0">
            <label class="form-label">Filter Mata Kuliah</label>
            <select name="mata_kuliah_id" class="form-control">
                <option value="">Semua Mata Kuliah</option>
                @foreach($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id')==$mk->id?'selected':'' }}>{{ $mk->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Filter</button>
        <a href="{{ route('kelas.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Mata Kuliah</th><th>Hari</th><th>Jam</th><th>Ruangan</th><th>Status</th><th style="text-align:center">Aksi</th></tr></thead>
            <tbody>
                @forelse($kelas as $k)
                <tr>
                    <td>
                        <strong>{{ $k->mataKuliah->nama }}</strong>
                        <div style="font-size:0.75rem;color:var(--text-muted)">{{ $k->mataKuliah->kode }}</div>
                    </td>
                    <td><span class="badge badge-info">{{ $k->hari }}</span></td>
                    <td style="font-size:0.82rem">{{ substr($k->jam_mulai,0,5) }} – {{ substr($k->jam_selesai,0,5) }}</td>
                    <td style="color:var(--text-secondary)">{{ $k->ruangan ?? '-' }}</td>
                    <td><span class="badge {{ $k->is_aktif?'badge-success':'badge-warning' }}">{{ $k->is_aktif?'Aktif':'Nonaktif' }}</span></td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:0.4rem;justify-content:center">
                            <a href="{{ route('kelas.edit', $k->id) }}" class="btn btn-secondary btn-sm">✏️</a>
                            <form method="POST" action="{{ route('kelas.destroy', $k->id) }}" onsubmit="return confirm('Hapus jadwal ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted)">Belum ada jadwal kelas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $kelas->links() }}</div>
</div>
@endsection
