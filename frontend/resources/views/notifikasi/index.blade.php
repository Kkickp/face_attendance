@extends('layouts.app')
@section('title','Notifikasi')
@section('page-title','Notifikasi')
@section('page-subtitle','Riwayat semua notifikasi sistem')
@section('content')
<div class="page-header">
    <div><h1>Notifikasi</h1></div>
    <form method="POST" action="{{ route('notifikasi.markAllRead') }}">
        @csrf
        <button type="submit" class="btn btn-secondary">✅ Tandai Semua Dibaca</button>
    </form>
</div>

<div class="card">
    @forelse($notifikasi as $n)
    <div style="display:flex;align-items:flex-start;gap:1rem;padding:0.9rem 0;border-bottom:1px solid var(--border);{{ is_null($n->read_at)?'background:rgba(99,102,241,0.04);margin:0 -1.25rem;padding:0.9rem 1.25rem;':'opacity:0.7' }}">
        <div style="font-size:1.3rem;flex-shrink:0;margin-top:2px">🔔</div>
        <div style="flex:1">
            <div style="font-weight:600;font-size:0.86rem">{{ $n->data['judul'] ?? 'Notifikasi' }}</div>
            <div style="font-size:0.8rem;color:var(--text-secondary);margin-top:2px">{{ $n->data['pesan'] ?? '' }}</div>
            <div style="font-size:0.72rem;color:var(--text-muted);margin-top:4px">{{ \Carbon\Carbon::parse($n->created_at)->diffForHumans() }}</div>
        </div>
        @if(is_null($n->read_at))
            <span class="badge badge-purple" style="flex-shrink:0">Baru</span>
        @endif
        <form method="POST" action="{{ route('notifikasi.destroy', $n->id) }}">
            @csrf @method('DELETE')
            <button type="submit" style="background:none;border:none;cursor:pointer;color:var(--text-muted);font-size:1rem" title="Hapus">✕</button>
        </form>
    </div>
    @empty
    <div style="text-align:center;padding:3rem;color:var(--text-muted)">
        <div style="font-size:2.5rem;margin-bottom:0.75rem">🔔</div>
        <p>Tidak ada notifikasi.</p>
    </div>
    @endforelse
    <div style="margin-top:1rem">{{ $notifikasi->links() }}</div>
</div>
@endsection
