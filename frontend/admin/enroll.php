<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Pendaftaran Wajah Master</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <div class="glass-panel" style="width: 100%; max-width: 500px;">
        <h1 class="title">Enrollment Wajah</h1>
        
        <div class="form-group">
            <label>NIM Mahasiswa</label>
            <input type="text" id="nim" placeholder="Masukkan NIM">
        </div>
        <div class="form-group">
            <label>Nama Lengkap</label>
            <input type="text" id="nama" placeholder="Masukkan Nama Lengkap">
        </div>
        
        <div class="video-wrapper" style="width:100%; height:300px; margin-bottom:1rem; border-radius:12px;">
            <video id="enrollWebcam" autoplay playsinline></video>
        </div>
        
        <button class="btn" style="width:100%;" onclick="enrollFace()">Daftarkan Wajah</button>
        <div id="enrollMsg" style="margin-top:1rem; text-align:center;"></div>
        
        <div style="margin-top:2rem; text-align:center;">
            <a href="dashboard.php" style="color:#38bdf8; text-decoration:none;">Kembali ke Dashboard</a>
        </div>
    </div>

    <script>
        const video = document.getElementById('enrollWebcam');
        const msgBox = document.getElementById('enrollMsg');

        // Setup Webcam
        navigator.mediaDevices.getUserMedia({ video: true })
            .then(stream => { video.srcObject = stream; })
            .catch(err => { msgBox.innerHTML = "<span class='error'>❌ Akses kamera ditolak</span>"; });

        async function enrollFace() {
            const nim = document.getElementById('nim').value;
            const nama = document.getElementById('nama').value;
            
            if(!nim || !nama) {
                msgBox.innerHTML = "<span class='error'>⚠️ NIM dan Nama wajib diisi!</span>";
                return;
            }
            
            if(video.videoWidth === 0 || video.videoHeight === 0) {
                msgBox.innerHTML = "<span class='error'>⚠️ Kamera belum siap atau tidak terdeteksi. Pastikan webcam menyala.</span>";
                return;
            }
            
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
            const base64Image = canvas.toDataURL('image/jpeg', 0.9);
            
            msgBox.innerHTML = "<span class='warning'>⏳ Memproses pendaftaran...</span>";
            
            try {
                const response = await fetch("http://localhost:8000/api/enroll", {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        nim: nim,
                        nama_lengkap: nama,
                        foto_base64: base64Image
                    })
                });
                
                const data = await response.json();
                
                if(data.status === 'success') {
                    msgBox.innerHTML = `<span class='success'>✅ ${data.message}</span>`;
                    document.getElementById('nim').value = '';
                    document.getElementById('nama').value = '';
                } else {
                    msgBox.innerHTML = `<span class='error'>❌ ${data.message}</span>`;
                }
            } catch (err) {
                msgBox.innerHTML = `<span class='error'>❌ Gagal menghubungi server Python. Pastikan FastAPI berjalan.</span>`;
            }
        }
    </script>
</body>
</html>
