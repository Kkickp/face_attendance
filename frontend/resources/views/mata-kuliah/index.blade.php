@extends('layouts.app')
@section('title','Mata Kuliah')
@section('page-title','Manajemen Mata Kuliah')
@section('page-subtitle','Kelola daftar mata kuliah')
@section('content')
<div class="page-header">
    <div><h1>Mata Kuliah</h1><p>Total: <strong>{{ $mataKuliah->total() }}</strong></p></div>
    <a href="{{ route('mata-kuliah.create') }}" class="btn btn-primary">➕ Tambah Mata Kuliah</a>
</div>
<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0">
            <label class="form-label">Cari</label>
            <input type="text" name="cari" class="form-control" placeholder="Kode atau nama..." value="{{ request('cari') }}">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        <a href="{{ route('mata-kuliah.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Kode</th><th>Nama Mata Kuliah</th><th>SKS</th><th>Dosen</th><th>Kelas</th><th>Status</th><th style="text-align:center">Aksi</th></tr></thead>
            <tbody>
                @forelse($mataKuliah as $mk)
                <tr>
                    <td><span class="badge badge-info">{{ $mk->kode }}</span></td>
                    <td><strong>{{ $mk->nama }}</strong></td>
                    <td style="text-align:center">{{ $mk->sks }} SKS</td>
                    <td style="color:var(--text-secondary);font-size:0.82rem">{{ $mk->dosen_pengampu ?? '-' }}</td>
                    <td style="text-align:center"><span class="badge badge-purple">{{ $mk->kelas_count }} kelas</span></td>
                    <td><span class="badge {{ $mk->is_aktif ? 'badge-success' : 'badge-warning' }}">{{ $mk->is_aktif ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:0.4rem;justify-content:center">
                            <a href="{{ route('mata-kuliah.edit', $mk->id) }}" class="btn btn-secondary btn-sm">✏️</a>
                            <form method="POST" action="{{ route('mata-kuliah.destroy', $mk->id) }}" onsubmit="return confirm('Hapus {{ $mk->nama }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:3rem;color:var(--text-muted)">Belum ada mata kuliah. <a href="{{ route('mata-kuliah.create') }}" style="color:var(--accent-light)">Tambah sekarang</a></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $mataKuliah->links() }}</div>
</div>
@endsection
