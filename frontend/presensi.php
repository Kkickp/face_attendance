<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Presensi Wajah</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>

    <div class="glass-panel">
        <h1 class="title">Face Recognition Attendance</h1>
        
        <div class="scanner-container">
            <div class="video-wrapper">
                <video id="webcam" autoplay playsinline></video>
                <div class="overlay"></div>
            </div>
            
            <div class="status-panel glass-panel">
                <div id="statusIcon" class="status-icon">👤</div>
                <div id="statusText" class="status-text neutral">Menunggu wajah...</div>
                <div style="margin-top: 2rem;">
                    <a href="admin/dashboard.php" style="color:#38bdf8; text-decoration:none;">Admin Login</a>
                </div>
            </div>
        </div>
    </div>

    <script src="assets/scanner.js"></script>
</body>
</html>
