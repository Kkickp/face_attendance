@extends('layouts.app')
@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Selamat datang, ' . auth()->user()->name)

@section('content')
{{-- Stat Cards --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple">👤</div>
        <div>
            <div class="stat-value">{{ $totalMahasiswa }}</div>
            <div class="stat-label">Total Mahasiswa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue">📚</div>
        <div>
            <div class="stat-value">{{ $totalMataKuliah }}</div>
            <div class="stat-label">Mata Kuliah Aktif</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green">✅</div>
        <div>
            <div class="stat-value">{{ $presensiHariIni }}</div>
            <div class="stat-label">Presensi Hari Ini</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon amber">🎯</div>
        <div>
            <div class="stat-value">{{ $sesiAktif }}</div>
            <div class="stat-label">Sesi Aktif Sekarang</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 340px;gap:1.25rem;align-items:start;flex-wrap:wrap;">

    {{-- Grafik Kehadiran --}}
    <div class="card">
        <div class="card-title">📈 Kehadiran 7 Hari Terakhir</div>
        <canvas id="grafikChart" height="90"></canvas>
    </div>

    {{-- Status Engine & Sesi --}}
    <div style="display:flex;flex-direction:column;gap:1rem;">
        <div class="card">
            <div class="card-title">⚙️ Status Mesin AI</div>
            <div style="display:flex;align-items:center;gap:0.6rem;margin-top:0.5rem;">
                <span class="status-dot {{ $pythonAktif ? 'online' : 'offline' }}"></span>
                <span style="font-size:0.85rem;font-weight:600;">
                    {{ $pythonAktif ? 'Python Engine Aktif' : 'Python Engine Mati' }}
                </span>
            </div>
            @if(!$pythonAktif)
                <p style="font-size:0.75rem;color:var(--danger-light);margin-top:0.5rem;">
                    Jalankan: <code>uvicorn main:app</code> di folder backend
                </p>
            @endif
        </div>

        <div class="card">
            <div class="card-title">🎯 Sesi Aktif Hari Ini</div>
            @if($sesiAktif > 0)
                <div class="badge badge-success" style="margin-top:0.5rem;">{{ $sesiAktif }} sesi terbuka</div>
            @else
                <div style="font-size:0.8rem;color:var(--text-muted);margin-top:0.5rem;">Belum ada sesi dibuka</div>
            @endif
            <a href="{{ route('sesi.index') }}" class="btn btn-primary btn-sm" style="margin-top:0.75rem;">Kelola Sesi</a>
        </div>
    </div>
</div>

{{-- Presensi Terbaru --}}
<div class="card" style="margin-top:1.25rem;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:1rem;">
        <div class="card-title" style="margin-bottom:0;">🕐 Presensi Terbaru</div>
        <a href="{{ route('laporan.index') }}" class="btn btn-secondary btn-sm">Lihat Semua</a>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Mahasiswa</th>
                    <th>NIM</th>
                    <th>Mata Kuliah</th>
                    <th>Waktu</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($presensiTerbaru as $p)
                    <tr>
                        <td><strong>{{ $p->mahasiswa->nama_lengkap ?? '-' }}</strong></td>
                        <td><span style="color:var(--text-secondary);font-size:0.78rem;">{{ $p->nim }}</span></td>
                        <td>{{ $p->kelas->mataKuliah->nama ?? '-' }}</td>
                        <td style="color:var(--text-secondary);font-size:0.8rem;">{{ \Carbon\Carbon::parse($p->waktu_presensi)->format('d/m H:i') }}</td>
                        <td>
                            <span class="badge {{ $p->status === 'Hadir' ? 'badge-success' : 'badge-danger' }}">
                                {{ $p->status }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="5" style="text-align:center;color:var(--text-muted);padding:2rem;">Belum ada data presensi hari ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const ctx = document.getElementById('grafikChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: {!! json_encode(array_column($grafik, 'tanggal')) !!},
        datasets: [{
            label: 'Jumlah Presensi',
            data: {!! json_encode(array_column($grafik, 'jumlah')) !!},
            backgroundColor: 'rgba(99,102,241,0.3)',
            borderColor: 'rgba(99,102,241,0.9)',
            borderWidth: 2,
            borderRadius: 6,
        }]
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#8892a4' } },
            y: { grid: { color: 'rgba(255,255,255,0.05)' }, ticks: { color: '#8892a4', precision: 0 }, beginAtZero: true }
        }
    }
});
</script>
@endpush
