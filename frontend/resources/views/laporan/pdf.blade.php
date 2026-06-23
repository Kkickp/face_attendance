<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Laporan Presensi</title>
<style>
    body{font-family:Arial,sans-serif;font-size:11px;color:#222;margin:0;padding:20px}
    h2{text-align:center;margin-bottom:4px;font-size:14px}
    p.sub{text-align:center;color:#555;margin-bottom:16px;font-size:10px}
    table{width:100%;border-collapse:collapse}
    th{background:#1e1b4b;color:white;padding:6px 8px;text-align:left;font-size:10px}
    td{padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:10px}
    tr:nth-child(even)td{background:#f9fafb}
    .badge-hadir{background:#d1fae5;color:#065f46;padding:2px 6px;border-radius:4px}
    .badge-lain{background:#fee2e2;color:#991b1b;padding:2px 6px;border-radius:4px}
    .footer{margin-top:20px;text-align:right;font-size:9px;color:#777}
</style>
</head>
<body>
<h2>📋 Laporan Presensi Mahasiswa</h2>
<p class="sub">Dicetak pada: {{ now()->format('d M Y H:i') }}</p>
<table>
    <thead>
        <tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Mata Kuliah</th><th>Waktu Presensi</th><th>Status</th></tr>
    </thead>
    <tbody>
        @foreach($presensi as $i => $p)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $p->nim }}</td>
            <td>{{ $p->mahasiswa->nama_lengkap ?? '-' }}</td>
            <td>{{ $p->kelas->mataKuliah->nama ?? '-' }}</td>
            <td>{{ \Carbon\Carbon::parse($p->waktu_presensi)->format('d/m/Y H:i') }}</td>
            <td><span class="{{ $p->status==='Hadir'?'badge-hadir':'badge-lain' }}">{{ $p->status }}</span></td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">Total: {{ count($presensi) }} data &nbsp;|&nbsp; Sistem Presensi Wajah</div>
</body>
</html>
