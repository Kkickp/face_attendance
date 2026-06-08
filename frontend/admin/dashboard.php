<?php
require_once '../db.php';

// Ambil data presensi terbaru
$stmt = $pdo->query("
    SELECT p.id, p.nim, m.nama_lengkap, p.waktu_presensi, p.status 
    FROM presensi p 
    JOIN mahasiswa m ON p.nim = m.nim 
    ORDER BY p.waktu_presensi DESC 
    LIMIT 50
");
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Presensi</title>
    <link rel="stylesheet" href="../assets/style.css">
</head>
<body>

    <div class="glass-panel" style="width: 80%; max-width: 1000px;">
        <h1 class="title">Admin Dashboard</h1>
        
        <div style="display:flex; justify-content:space-between; margin-bottom:1rem;">
            <a href="enroll.php" class="btn">➕ Daftarkan Wajah Baru</a>
            <a href="../presensi.php" class="btn" style="background:transparent; border:1px solid #38bdf8;">🖥️ Kembali ke Scanner</a>
        </div>

        <h3>Riwayat Presensi Terbaru</h3>
        <table>
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>NIM</th>
                    <th>Nama Lengkap</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($logs as $log): ?>
                <tr>
                    <td><?= $log['waktu_presensi'] ?></td>
                    <td><?= $log['nim'] ?></td>
                    <td><?= $log['nama_lengkap'] ?></td>
                    <td>
                        <?php if($log['status'] == 'Hadir'): ?>
                            <span class="success">✔️ Hadir</span>
                        <?php else: ?>
                            <span class="error"><?= $log['status'] ?></span>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(count($logs) == 0): ?>
                <tr><td colspan="4" style="text-align:center;">Belum ada data presensi</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

</body>
</html>
