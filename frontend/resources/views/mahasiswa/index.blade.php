@extends('layouts.app')
@section('title','Mahasiswa')
@section('page-title','Manajemen Mahasiswa')
@section('page-subtitle','Daftar mahasiswa terdaftar & wajah terenkripsi')

@section('content')
<div class="page-header">
    <div>
        <h1>Mahasiswa</h1>
        <p>Total: <strong>{{ $mahasiswa->total() }}</strong> mahasiswa</p>
    </div>
    <a href="{{ route('mahasiswa.create') }}" class="btn btn-primary">➕ Tambah Mahasiswa</a>
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="flex:1;min-width:220px;margin-bottom:0">
            <label class="form-label">Cari NIM / Nama</label>
            <input type="text" name="cari" class="form-control" placeholder="Ketik NIM atau nama..." value="{{ request('cari') }}">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Cari</button>
        <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Terdaftar</th>
                    <th style="text-align:center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswa as $mhs)
                <tr>
                    <td><span class="badge badge-purple">{{ $mhs->nim }}</span></td>
                    <td><strong>{{ $mhs->nama_lengkap }}</strong></td>
                    <td style="color:var(--text-secondary);font-size:0.8rem">{{ $mhs->created_at?->format('d M Y') }}</td>
                    <td style="text-align:center">
                        <div style="display:flex;gap:0.4rem;justify-content:center">
                            <a href="{{ route('mahasiswa.edit', $mhs->nim) }}" class="btn btn-secondary btn-sm">✏️ Edit</a>
                            <form method="POST" action="{{ route('mahasiswa.destroy', $mhs->nim) }}" onsubmit="return confirm('Hapus {{ $mhs->nama_lengkap }}? Data presensinya ikut terhapus.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">🗑️ Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:3rem;color:var(--text-muted)">
                    <div style="font-size:2.5rem;margin-bottom:0.5rem">👤</div>
                    Belum ada mahasiswa. <a href="{{ route('mahasiswa.create') }}" style="color:var(--accent-light)">Tambah sekarang</a>
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $mahasiswa->links() }}</div>
</div>
@endsection
