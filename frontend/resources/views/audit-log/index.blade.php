@extends('layouts.app')
@section('title','Audit Log')
@section('page-title','Audit Log')
@section('page-subtitle','Rekam jejak semua aksi admin')
@section('content')
<div class="page-header">
    <div><h1>Audit Log</h1><p>Total: <strong>{{ $logs->total() }}</strong> entri</p></div>
</div>

<div class="filter-bar">
    <form method="GET" style="display:flex;gap:0.75rem;flex-wrap:wrap;align-items:flex-end;width:100%">
        <div class="form-group" style="flex:1;min-width:200px;margin-bottom:0">
            <label class="form-label">Cari</label>
            <input type="text" name="cari" class="form-control" placeholder="Aksi atau keterangan..." value="{{ request('cari') }}">
        </div>
        <div class="form-group" style="margin-bottom:0">
            <label class="form-label">Tanggal</label>
            <input type="date" name="tanggal" class="form-control" value="{{ request('tanggal') }}">
        </div>
        <button type="submit" class="btn btn-primary">🔍 Filter</button>
        <a href="{{ route('audit-log.index') }}" class="btn btn-secondary">Reset</a>
    </form>
</div>

<div class="card">
    <div class="table-wrap">
        <table>
            <thead><tr><th>Waktu</th><th>Admin</th><th>Aksi</th><th>Keterangan</th><th>IP</th></tr></thead>
            <tbody>
                @forelse($logs as $log)
                <tr>
                    <td style="font-size:0.78rem;color:var(--text-secondary);white-space:nowrap">{{ \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i:s') }}</td>
                    <td style="font-size:0.82rem">{{ $log->user->name ?? '<i style="color:var(--text-muted)">Sistem</i>' }}</td>
                    <td><span class="badge badge-purple">{{ $log->aksi }}</span></td>
                    <td style="font-size:0.8rem;color:var(--text-secondary)">{{ $log->keterangan ?? '-' }}</td>
                    <td style="font-size:0.75rem;color:var(--text-muted)">{{ $log->ip_address ?? '-' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:3rem;color:var(--text-muted)">Belum ada audit log.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div style="margin-top:1rem">{{ $logs->links() }}</div>
</div>
@endsection
