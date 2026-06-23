@extends('layouts.app')
@section('title','Sesi Presensi')
@section('page-title','Sesi Presensi')
@section('page-subtitle','Buka atau tutup sesi presensi per kelas')
@section('content')

<div style="display:grid;grid-template-columns:340px 1fr;gap:1.5rem;align-items:start">

    {{-- Form Buka Sesi --}}
    <div class="card">
        <div class="card-title">🎯 Buka Sesi Baru</div>
        <form method="POST" action="{{ route('sesi.buka') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Pilih Kelas *</label>
                <select name="kelas_id" class="form-control" required>
                    <option value="">-- Pilih Kelas --</option>
                    @foreach($kelasAktif as $k)
                        <option value="{{ $k->id }}">{{ $k->mataKuliah->nama }} — {{ $k->hari }} {{ substr($k->jam_mulai,0,5) }}</option>
                    @endforeach
                </select>
            </div>
            <p style="font-size:0.77rem;color:var(--text-muted);margin-bottom:1rem">Sesi akan dibuka untuk tanggal: <strong style="color:var(--text-secondary)">{{ now()->format('d M Y') }}</strong></p>
            <button type="submit" class="btn btn-success" style="width:100%">✅ Buka Sesi Presensi</button>
        </form>
    </div>

    {{-- Daftar Sesi --}}
    <div class="card">
        <div class="card-title">📋 Riwayat Sesi</div>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Mata Kuliah</th><th>Hari/Jam</th><th>Tanggal</th><th>Status</th><th>Dibuka Oleh</th><th style="text-align:center">Aksi</th></tr></thead>
                <tbody>
                    @forelse($sesi as $s)
                    <tr>
                        <td><strong>{{ $s->kelas->mataKuliah->nama ?? '-' }}</strong></td>
                        <td style="font-size:0.8rem;color:var(--text-secondary)">{{ $s->kelas->hari ?? '' }} {{ substr($s->kelas->jam_mulai??'',0,5) }}</td>
                        <td style="font-size:0.8rem">{{ $s->tanggal->format('d M Y') }}</td>
                        <td>
                            <span class="badge {{ $s->status==='buka'?'badge-success':'badge-warning' }}">
                                {{ $s->status==='buka'?'🟢 Buka':'🔴 Tutup' }}
                            </span>
                        </td>
                        <td style="font-size:0.8rem;color:var(--text-secondary)">{{ $s->dibukaDari->name ?? '-' }}</td>
                        <td style="text-align:center">
                            @if($s->status==='buka')
                                <form method="POST" action="{{ route('sesi.tutup', $s->id) }}" onsubmit="return confirm('Tutup sesi ini?')">
                                    @csrf @method('PATCH')
                                    <button type="submit" class="btn btn-danger btn-sm">🔒 Tutup</button>
                                </form>
                            @else
                                <span style="color:var(--text-muted);font-size:0.78rem">{{ $s->ditutup_pada?->format('H:i') ?? '-' }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" style="text-align:center;padding:2rem;color:var(--text-muted)">Belum ada sesi presensi.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div style="margin-top:1rem">{{ $sesi->links() }}</div>
    </div>
</div>
@endsection
