<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') — Sistem Presensi Wajah</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @stack('styles')
</head>
<body>
<div class="app-layout">

    {{-- ─── SIDEBAR ─── --}}
    <aside class="sidebar" id="sidebar">
        <div class="sidebar-logo">
            <div class="logo-icon">🎓</div>
            <div class="logo-text">
                Presensi Wajah
                <span>Face Recognition System</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <div class="nav-section-label">Utama</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <span class="nav-icon">📊</span> Dashboard
            </a>
            <a href="{{ route('presensi.index') }}" class="nav-item" target="_blank">
                <span class="nav-icon">📷</span> Halaman Presensi
            </a>

            <div class="nav-section-label">Manajemen</div>
            <a href="{{ route('mahasiswa.index') }}" class="nav-item {{ request()->routeIs('mahasiswa.*') ? 'active' : '' }}">
                <span class="nav-icon">👤</span> Mahasiswa
            </a>
            <a href="{{ route('mata-kuliah.index') }}" class="nav-item {{ request()->routeIs('mata-kuliah.*') ? 'active' : '' }}">
                <span class="nav-icon">📚</span> Mata Kuliah
            </a>
            <a href="{{ route('kelas.index') }}" class="nav-item {{ request()->routeIs('kelas.*') ? 'active' : '' }}">
                <span class="nav-icon">🗓️</span> Kelas & Jadwal
            </a>
            <a href="{{ route('sesi.index') }}" class="nav-item {{ request()->routeIs('sesi.*') ? 'active' : '' }}">
                <span class="nav-icon">🎯</span> Sesi Presensi
            </a>

            <div class="nav-section-label">Laporan</div>
            <a href="{{ route('laporan.index') }}" class="nav-item {{ request()->routeIs('laporan.*') ? 'active' : '' }}">
                <span class="nav-icon">📋</span> Log Presensi
            </a>
            <a href="{{ route('rekap.index') }}" class="nav-item {{ request()->routeIs('rekap.*') ? 'active' : '' }}">
                <span class="nav-icon">📈</span> Rekap Kehadiran
            </a>

            <div class="nav-section-label">Sistem</div>
            <a href="{{ route('notifikasi.index') }}" class="nav-item {{ request()->routeIs('notifikasi.*') ? 'active' : '' }}">
                <span class="nav-icon">🔔</span> Notifikasi
                @php $unread = auth()->user()->unreadNotifications()->count(); @endphp
                @if($unread > 0)
                    <span class="badge badge-danger ms-auto" style="margin-left:auto;">{{ $unread }}</span>
                @endif
            </a>
            <a href="{{ route('audit-log.index') }}" class="nav-item {{ request()->routeIs('audit-log.*') ? 'active' : '' }}">
                <span class="nav-icon">🕵️</span> Audit Log
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="nav-item" style="width:100%;background:none;border:none;cursor:pointer;color:var(--danger-light);font-family:inherit;">
                    <span class="nav-icon">🚪</span> Logout
                </button>
            </form>
        </div>
    </aside>

    {{-- ─── MAIN ─── --}}
    <div class="main-content">
        {{-- Topbar --}}
        <header class="topbar">
            <div class="topbar-title">
                @yield('page-title', 'Dashboard')
                <small>@yield('page-subtitle', 'Sistem Presensi Berbasis Wajah')</small>
            </div>
            <div class="topbar-actions">
                {{-- Bell Icon --}}
                <a href="{{ route('notifikasi.index') }}" class="btn-icon" title="Notifikasi" id="bell-btn">
                    🔔
                    @if($unread > 0)
                        <span class="notif-badge" id="notif-badge">{{ $unread > 99 ? '99+' : $unread }}</span>
                    @endif
                </a>

                {{-- User menu --}}
                <div class="user-menu">
                    <div class="avatar">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</div>
                    <span class="user-name">{{ auth()->user()->name }}</span>
                </div>
            </div>
        </header>

        {{-- Page Content --}}
        <main class="page-content">
            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="alert alert-success">✅ {{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">❌ {{ session('error') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-danger">
                    <div>
                        @foreach($errors->all() as $error)
                            <div>• {{ $error }}</div>
                        @endforeach
                    </div>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

{{-- Toast Container --}}
<div class="toast-container" id="toastContainer"></div>

<script>
// Toast helper
function showToast(type, title, msg, duration = 4000) {
    const icons = { success: '✅', error: '❌', warning: '⚠️', info: 'ℹ️' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<div class="toast-icon">${icons[type] || 'ℹ️'}</div><div><div class="toast-title">${title}</div><div class="toast-msg">${msg}</div></div>`;
    document.getElementById('toastContainer').appendChild(toast);
    setTimeout(() => { toast.style.animation = 'slideOut 0.35s ease forwards'; setTimeout(() => toast.remove(), 350); }, duration);
}

// Auto-dismiss alerts
document.querySelectorAll('.alert').forEach(a => setTimeout(() => a.style.opacity = '0', 5000));

// Mobile sidebar toggle
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    document.addEventListener('click', e => {
        if (window.innerWidth <= 768 && sidebar && !sidebar.contains(e.target)) {
            sidebar.classList.remove('open');
        }
    });
});
</script>
@stack('scripts')
</body>
</html>
