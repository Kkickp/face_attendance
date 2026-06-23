@extends('layouts.app')
@section('title','Tambah Mahasiswa')
@section('page-title','Tambah Mahasiswa')
@section('page-subtitle','Daftarkan mahasiswa baru & rekam wajah')

@section('content')
<div class="page-header">
    <div><h1>Tambah Mahasiswa</h1><p>Isi data lalu aktifkan kamera untuk merekam wajah</p></div>
    <a href="{{ route('mahasiswa.index') }}" class="btn btn-secondary">← Kembali</a>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.5rem;align-items:start">
    {{-- Form Data --}}
    <div class="card">
        <div class="card-title">📋 Data Mahasiswa</div>
        <form id="enrollForm" method="POST" action="{{ route('mahasiswa.store') }}">
            @csrf
            <input type="hidden" name="foto_base64" id="foto_base64">
            <div class="form-group">
                <label class="form-label">NIM <span style="color:var(--danger-light)">*</span></label>
                <input type="text" name="nim" class="form-control" value="{{ old('nim') }}" placeholder="Contoh: 2021001001" required>
            </div>
            <div class="form-group">
                <label class="form-label">Nama Lengkap <span style="color:var(--danger-light)">*</span></label>
                <input type="text" name="nama_lengkap" class="form-control" value="{{ old('nama_lengkap') }}" placeholder="Nama sesuai KTM" required>
            </div>
            <div style="background:rgba(245,158,11,0.08);border:1px solid rgba(245,158,11,0.2);border-radius:var(--radius);padding:0.75rem;margin-bottom:1rem;font-size:0.8rem;color:var(--warning-light)">
                ⚠️ Pastikan kamera sudah aktif dan wajah terlihat jelas sebelum submit.
            </div>
            <button type="submit" class="btn btn-primary" id="submitBtn" disabled style="width:100%">
                📤 Daftarkan Mahasiswa
            </button>
        </form>
    </div>

    {{-- Webcam --}}
    <div class="card">
        <div class="card-title">📷 Rekam Wajah</div>
        <div class="webcam-wrapper" id="webcamWrapper">
            <video id="webcamVideo" autoplay playsinline></video>
            <div class="webcam-overlay" id="webcamOverlay"></div>
        </div>
        <div style="margin-top:1rem;display:flex;gap:0.75rem;flex-wrap:wrap">
            <button class="btn btn-secondary" id="btnAktifkan" onclick="aktifkanKamera()">📹 Aktifkan Kamera</button>
            <button class="btn btn-success" id="btnAmbil" onclick="ambilFoto()" disabled>📸 Ambil Foto</button>
        </div>
        <canvas id="fotoCanvas" style="display:none"></canvas>
        <div id="fotoPreview" style="margin-top:1rem;display:none">
            <p style="font-size:0.8rem;color:var(--success-light);margin-bottom:0.5rem">✅ Foto berhasil diambil!</p>
            <img id="fotoImg" style="width:100%;border-radius:var(--radius);border:2px solid var(--success)">
            <button class="btn btn-warning btn-sm" onclick="ambilUlang()" style="margin-top:0.5rem">🔄 Ambil Ulang</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let stream = null;
const video   = document.getElementById('webcamVideo');
const canvas  = document.getElementById('fotoCanvas');
const overlay = document.getElementById('webcamOverlay');
const submitBtn = document.getElementById('submitBtn');

async function aktifkanKamera() {
    try {
        stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'user', width: 640, height: 480 } });
        video.srcObject = stream;
        overlay.className = 'webcam-overlay scanning';
        document.getElementById('btnAktifkan').disabled = true;
        document.getElementById('btnAmbil').disabled = false;
    } catch (e) {
        alert('Gagal mengakses kamera: ' + e.message);
    }
}

function ambilFoto() {
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    canvas.getContext('2d').drawImage(video, 0, 0);
    const base64 = canvas.toDataURL('image/jpeg', 0.85);
    document.getElementById('foto_base64').value = base64;
    document.getElementById('fotoImg').src = base64;
    document.getElementById('fotoPreview').style.display = 'block';
    overlay.className = 'webcam-overlay success';
    submitBtn.disabled = false;
    if (stream) stream.getTracks().forEach(t => t.stop());
}

function ambilUlang() {
    document.getElementById('fotoPreview').style.display = 'none';
    submitBtn.disabled = true;
    document.getElementById('btnAktifkan').disabled = false;
    document.getElementById('btnAmbil').disabled = true;
    overlay.className = 'webcam-overlay';
}

document.getElementById('enrollForm').addEventListener('submit', function() {
    submitBtn.textContent = '⏳ Mendaftarkan wajah...';
    submitBtn.disabled = true;
});
</script>
@endpush
