<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi Wajah</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:'Inter',sans-serif;background:#f8fafc;color:#1e293b;min-height:100vh;display:flex;flex-direction:column;align-items:center;padding:2rem 1rem;overflow-x:hidden;}
        
        .header {text-align:center;margin-bottom:2rem;z-index:10;}
        .logo-icon{width:70px;height:70px;background:#ffffff;border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2rem;margin:0 auto 1rem;box-shadow:0 4px 15px rgba(0,0,0,0.05);border:1px solid #e2e8f0;}
        h1{font-size:1.8rem;font-weight:800;letter-spacing:-0.02em;color:#0f172a;}
        p{color:#64748b;font-size:0.95rem;margin-top:0.5rem;}

        .card {background:#ffffff;border:1px solid #e2e8f0;border-radius:16px;padding:1.5rem;width:100%;max-width:540px;box-shadow:0 10px 25px rgba(0,0,0,0.05);z-index:10;}
        
        .form-group{margin-bottom:1.5rem;}
        .form-label{display:block;font-size:0.85rem;font-weight:600;color:#475569;margin-bottom:0.6rem;text-transform:uppercase;letter-spacing:0.05em;}
        .form-control{width:100%;background:#f8fafc;border:1px solid #cbd5e1;border-radius:12px;padding:0.85rem 1rem;color:#1e293b;font-family:'Inter',sans-serif;font-size:0.95rem;transition:all 0.2s;outline:none;appearance:none;}
        .form-control:focus{border-color:#3b82f6;box-shadow:0 0 0 3px rgba(59,130,246,0.15);}
        select.form-control option {background:#ffffff;}

        .webcam-container {position:relative;border-radius:12px;overflow:hidden;border:2px solid #e2e8f0;background:#000;aspect-ratio:4/3;margin-bottom:1.5rem;}
        video {width:100%;height:100%;object-fit:cover;}
        .scan-overlay {position:absolute;inset:0;border:3px solid transparent;border-radius:12px;pointer-events:none;transition:border-color 0.3s;overflow:hidden;}
        .scan-overlay.scanning {border-color:#3b82f6;}
        .scan-overlay.scanning::after {
            content: '';
            position: absolute;
            left: 0;
            right: 0;
            height: 3px;
            background: #3b82f6;
            box-shadow: 0 0 10px 3px rgba(59,130,246,0.4);
            animation: laserScan 2s infinite ease-in-out;
        }
        
        @keyframes laserScan {
            0%, 100% { top: 0; opacity: 0; }
            10%, 90% { opacity: 1; }
            50% { top: calc(100% - 3px); }
        }

        .btn{width:100%;padding:1rem;border-radius:12px;font-size:1rem;font-weight:700;border:none;cursor:pointer;transition:all 0.2s;display:flex;align-items:center;justify-content:center;gap:0.5rem;font-family:'Inter',sans-serif;}
        .btn-primary{background:#3b82f6;color:white;box-shadow:0 4px 10px rgba(59,130,246,0.25);}
        .btn-primary:hover{background:#2563eb;transform:translateY(-1px);box-shadow:0 6px 15px rgba(59,130,246,0.3);}
        .btn-primary:disabled{opacity:0.6;cursor:not-allowed;transform:none;}

        .empty-state{text-align:center;padding:3rem 1rem;}
        .empty-state .icon{font-size:3.5rem;margin-bottom:1rem;opacity:0.8;}

        .footer{margin-top:3rem;text-align:center;font-size:0.8rem;color:#64748b;}
        .footer a{color:#3b82f6;text-decoration:none;font-weight:500;}
        .footer a:hover{text-decoration:underline;}

        /* Toast UI */
        .toast-container{position:fixed;top:2rem;left:50%;transform:translateX(-50%);z-index:9999;display:flex;flex-direction:column;gap:0.5rem;width:90%;max-width:400px;}
        .toast{background:#ffffff;border:1px solid #e2e8f0;border-radius:12px;padding:1rem 1.25rem;box-shadow:0 10px 25px rgba(0,0,0,0.1);display:flex;align-items:center;gap:0.75rem;animation:slideDown 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards; color:#1e293b;}
        .toast.success{border-left:4px solid #10b981;}
        .toast.error{border-left:4px solid #ef4444;}
        .toast.warning{border-left:4px solid #f59e0b;}
        
        @keyframes slideDown{
            from{transform:translateY(-100%);opacity:0;}
            to{transform:translateY(0);opacity:1;}
        }
    </style>
</head>
<body>
    <div class="toast-container" id="toastContainer"></div>

    <div class="header">
        <h1>Presensi Wajah</h1>
        <div id="digitalClock" style="font-size: 2rem; font-weight: 700; color: #3b82f6; font-family: monospace; margin-top: 10px;">00:00:00</div>
        <p>Arahkan wajah ke kamera untuk melakukan presensi</p>
    </div>

    <div class="card">
        @if($sesiAktif->count() > 0)
            <div class="form-group">
                <label class="form-label">Pilih Kelas / Sesi Presensi</label>
                <select id="sesi_id" class="form-control">
                    @foreach($sesiAktif as $sesi)
                        <option value="{{ $sesi->id }}">
                            {{ $sesi->kelas->mataKuliah->nama }} ({{ substr($sesi->kelas->jam_mulai,0,5) }} - {{ substr($sesi->kelas->jam_selesai,0,5) }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="webcam-container">
                <video id="webcam" autoplay playsinline></video>
                <div class="scan-overlay" id="overlay"></div>
            </div>

            <button id="btnScan" class="btn btn-primary">
                <span>📸 Scan & Presensi</span>
            </button>
            <canvas id="canvas" style="display:none;"></canvas>
        @else
            <div class="empty-state">
                <div class="icon">😴</div>
                <h3 style="font-size:1.2rem;margin-bottom:0.5rem;color:#1e293b;">Belum Ada Sesi</h3>
                <p>Tidak ada jadwal kelas atau sesi presensi yang sedang dibuka saat ini.</p>
            </div>
        @endif
    </div>

    <div class="footer">
        &copy; {{ date('Y') }} Sistem Face Attendance &nbsp;&bull;&nbsp; <a href="{{ route('login') }}">Login Admin</a>
    </div>

    @if($sesiAktif->count() > 0)
    <script>
        const video = document.getElementById('webcam');
        const canvas = document.getElementById('canvas');
        const overlay = document.getElementById('overlay');
        const btnScan = document.getElementById('btnScan');
        const sesiSelect = document.getElementById('sesi_id');
        let stream = null;

        // Digital Clock
        function updateClock() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit' });
            document.getElementById('digitalClock').textContent = timeString;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Initialize webcam
        async function initCamera() {
            try {
                stream = await navigator.mediaDevices.getUserMedia({ 
                    video: { facingMode: 'user', width: 640, height: 480 } 
                });
                video.srcObject = stream;
            } catch (err) {
                showToast('error', 'Gagal mengakses kamera. Pastikan memberikan izin akses.');
                btnScan.disabled = true;
            }
        }

        initCamera();

        btnScan.addEventListener('click', async () => {
            if (!stream) return;

            // Animasi scanning
            overlay.className = 'scan-overlay scanning';
            const originalBtnHtml = btnScan.innerHTML;
            btnScan.innerHTML = '<span>⏳ Memproses Wajah...</span>';
            btnScan.disabled = true;

            // Capture image
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0);
            const base64Image = canvas.toDataURL('image/jpeg', 0.85);

            try {
                const response = await fetch('{{ route("presensi.proses") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        foto_base64: base64Image,
                        sesi_id: sesiSelect.value
                    })
                });

                const result = await response.json();
                
                // Reset styling
                overlay.className = 'scan-overlay';
                
                if (result.status === 'success') {
                    showToast('success', result.message);
                    overlay.style.borderColor = '#10b981'; // green
                } else if (result.status === 'warning') {
                    showToast('warning', result.message);
                    overlay.style.borderColor = '#f59e0b'; // amber
                } else {
                    showToast('error', result.message || 'Wajah tidak dikenali atau terjadi kesalahan.');
                    overlay.style.borderColor = '#ef4444'; // red
                }

                // Reset border color after 3s
                setTimeout(() => { overlay.style.borderColor = 'transparent'; }, 3000);

            } catch (error) {
                overlay.className = 'scan-overlay';
                showToast('error', 'Terjadi kesalahan koneksi ke server.');
            } finally {
                btnScan.innerHTML = originalBtnHtml;
                btnScan.disabled = false;
            }
        });

        function showToast(type, message) {
            const container = document.getElementById('toastContainer');
            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            
            let icon = 'ℹ️';
            if(type === 'success') icon = '✅';
            if(type === 'error') icon = '❌';
            if(type === 'warning') icon = '⚠️';

            toast.innerHTML = `<div style="font-size:1.5rem">${icon}</div><div style="font-size:0.95rem;font-weight:500;">${message}</div>`;
            
            // Hapus toast lama jika ada agar tidak menumpuk terlalu banyak
            if(container.children.length > 2) {
                container.removeChild(container.firstChild);
            }
            
            container.appendChild(toast);
            
            setTimeout(() => {
                toast.style.animation = 'slideDown 0.4s reverse forwards';
                setTimeout(() => toast.remove(), 400);
            }, 5000);
        }
    </script>
    @endif
</body>
</html>
