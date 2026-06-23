@extends('layouts.app')
@section('title','Rekap Kehadiran')
@section('page-title','Rekap % Kehadiran')
@section('page-subtitle','Persentase kehadiran per mahasiswa per mata kuliah')
@section('content')
<div class="page-header">
    <div><h1>Rekap Kehadiran</h1></div>
    @if(request('mata_kuliah_id') && $rekap->count())
    <div style="display:flex;gap:0.5rem">
        <a href="{{ route('rekap.pdf', request()->query()) }}" class="btn btn-danger btn-sm" target="_blank">📄 PDF</a>
        <a href="{{ route('rekap.excel', request()->query()) }}" class="btn btn-success btn-sm">📊 Excel</a>
    </div>
    @endif
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="flex:1;min-width:220px;margin-bottom:0">
            <label class="form-label">Pilih Mata Kuliah *</label>
            <select name="mata_kuliah_id" class="form-control" required>
                <option value="">-- Pilih Mata Kuliah --</option>
                @foreach($mataKuliah as $mk)
                    <option value="{{ $mk->id }}" {{ request('mata_kuliah_id')==$mk->id?'selected':'' }}>{{ $mk->nama }}</option>
                @endforeach
            </select>
        </div>
        <button type="submit" class="btn btn-primary">📈 Tampilkan Rekap</button>
    </form>
</div>

@if($rekap->count())
<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Hadir</th><th>Total Sesi</th><th>% Kehadiran</th><th>Keterangan</th></tr></thead>
            <tbody>
                @foreach($rekap as $i => $r)
                <tr>
                    <td style="color:var(--text-muted)">{{ $i+1 }}</td>
                    <td><span class="badge badge-purple">{{ $r['nim'] }}</span></td>
                    <td><strong>{{ $r['nama'] }}</strong></td>
                    <td style="text-align:center;font-weight:600">{{ $r['hadir'] }}</td>
                    <td style="text-align:center;color:var(--text-secondary)">{{ $r['total_sesi'] }}</td>
                    <td style="min-width:160px">
                        <div style="display:flex;align-items:center;gap:0.75rem">
                            <div class="progress-bar" style="flex:1">
                                <div class="progress-fill {{ $r['badge'] }}" style="width:{{ $r['persen'] }}%"></div>
                            </div>
                            <span style="font-weight:700;font-size:0.85rem;min-width:40px;text-align:right">{{ $r['persen'] }}%</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge badge-{{ $r['badge'] }}">
                            {{ $r['persen'] >= 75 ? '✅ Memenuhi' : ($r['persen'] >= 50 ? '⚠️ Perhatian' : '❌ Tidak Memenuhi') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@elseif(request('mata_kuliah_id'))
<div class="card" style="text-align:center;padding:3rem">
    <div style="font-size:2.5rem;margin-bottom:0.75rem">📊</div>
    <p style="color:var(--text-muted)">Belum ada data presensi untuk mata kuliah ini.</p>
</div>
@else
<div class="card" style="text-align:center;padding:3rem">
    <div style="font-size:2.5rem;margin-bottom:0.75rem">📈</div>
    <p style="color:var(--text-muted)">Pilih mata kuliah di atas untuk melihat rekap kehadiran.</p>
</div>
@endif
@endsection
