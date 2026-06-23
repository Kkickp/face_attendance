@extends('layouts.app')
@section('title','Laporan Presensi')
@section('page-title','Laporan Presensi')
@section('page-subtitle','Log kehadiran mahasiswa')
@section('content')
<div class="page-header">
    <div><h1>Log Presensi</h1><p>Total: <strong>{{ $presensi->total() }}</strong> data</p></div>
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('laporan.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">📄 Export PDF</a>
        <a href="{{ route('laporan.excel', request()->query()) }}" class="btn btn-success btn-sm">📊 Export Excel</a>
    </div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Dari Tanggal</label>
            <input type="date" name="tanggal_dari" class="form-control" value="{{ request('tanggal_dari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Sampai Tanggal</label>
            <input type="date" name="tanggal_sampai" class="form-control" value="{{ request('tanggal_sampai') }}">
        </div>
        <div class="form-group" style="margin-bottom:0;min-width:180px">
            <label class="form-label">Mata Kuliah</label>
            <select name="mata_kuliah_id" class="form-control">
                <option value="">Semua</option>
                @foreach($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id')==$mk->id?'selected':'' }}>{{ $mk->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">NIM</label>
            <input type="text" name="nim" class="form-control" placeholder="NIM mahasiswa" value="{{ request('nim') }}">
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Status</label>
            <select name="status" class="form-control">
                <option value="">Semua</option>
                <option value="Hadir" {{ request('status')=='Hadir'?'selected':'' }}>Hadir</option>
                <option value="Spoofing" {{ request('status')=='Spoofing'?'selected':'' }}>Spoofing</option>
                <option value="Gagal" {{ request('status')=='Gagal'?'selected':'' }}>Gagal</option>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>NIM</th><th>Nama</th><th>Mata Kuliah</th><th>Hari/Jam</th><th>Waktu Presensi</th><th>Status</th></tr></thead>
            <tbody>
                @forelse($presensi as $p)
                <tr>
                    <td><span class="badge badge-purple">{{ $p->nim }}</span></td>
                    <td><strong>{{ $p->mahasiswa->nama_lengkap ?? '-' }}</strong></td>
                    <td>{{ $p->kelas->mataKuliah->nama ?? '-' }}</td>
                    <td style="font-size:0.8rem;color:var(--text-secondary)">{{ $p->kelas->hari ?? '' }} {{ substr($p->kelas->jam_mulai??'',0,5) }}</td>
                    <td style="font-size:0.8rem">{{ \Carbon\Carbon::parse($p->waktu_presensi)->format('d/m/Y H:i') }}</td>
                    <td><span class="badge {{ $p->status==='Hadir'?'badge-success':($p->status==='Spoofing'?'badge-warning':'badge-danger') }}">{{ $p->status }}</span></td>
                </tr>
                @empty
                <tr><td colspan="6" style="text-align:center;padding:3rem;color:var(--text-muted)">Tidak ada data presensi.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $presensi->links() }}</div>
</div>
@endsection
