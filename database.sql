CREATE DATABASE IF NOT EXISTS face_attendance_db;
USE face_attendance_db;

CREATE TABLE IF NOT EXISTS mahasiswa (
    nim VARCHAR(20) PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    face_encoding TEXT NOT NULL COMMENT 'Penyimpanan vektor wajah dalam format JSON',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS presensi (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nim VARCHAR(20) NOT NULL,
    waktu_presensi TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    status VARCHAR(50) NOT NULL COMMENT 'Hadir / Spoofing / Gagal',
    foto_bukti LONGTEXT COMMENT 'Foto base64 saat presensi untuk audit admin',
    FOREIGN KEY (nim) REFERENCES mahasiswa(nim) ON DELETE CASCADE
);
