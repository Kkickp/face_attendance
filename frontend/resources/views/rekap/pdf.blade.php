<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Rekap Kehadiran</title>
<style>
    body{font-family:Arial,sans-serif;font-size:11px;color:#222;margin:0;padding:20px}
    h2{text-align:center;margin-bottom:4px;font-size:14px}
    p.sub{text-align:center;color:#555;margin-bottom:16px;font-size:10px}
    table{width:100%;border-collapse:collapse}
    th{background:#1e1b4b;color:white;padding:6px 8px;text-align:left;font-size:10px}
    td{padding:5px 8px;border-bottom:1px solid #e5e7eb;font-size:10px}
    tr:nth-child(even)td{background:#f9fafb}
    .ok{color:#065f46;font-weight:bold}
    .warn{color:#92400e;font-weight:bold}
    .bad{color:#991b1b;font-weight:bold}
    .footer{margin-top:20px;text-align:right;font-size:9px;color:#777}
</style>
</head>
<body>
<h2>📈 Rekap Kehadiran — {{ $mk->nama }}</h2>
<p class="sub">Total Sesi: {{ $totalSesi }} &nbsp;|&nbsp; Dicetak: {{ now()->format('d M Y H:i') }}</p>
<table>
    <thead>
        <tr><th>No</th><th>NIM</th><th>Nama Mahasiswa</th><th>Hadir</th><th>Total Sesi</th><th>% Kehadiran</th><th>Keterangan</th></tr>
    </thead>
    <tbody>
        @foreach($rekap as $i => $r)
        <tr>
            <td>{{ $i+1 }}</td>
            <td>{{ $r['nim'] }}</td>
            <td>{{ $r['nama'] }}</td>
            <td style="text-align:center">{{ $r['hadir'] }}</td>
            <td style="text-align:center">{{ $r['total_sesi'] }}</td>
            <td style="text-align:center;font-weight:bold">{{ $r['persen'] }}%</td>
            <td class="{{ $r['persen']>=75?'ok':($r['persen']>=50?'warn':'bad') }}">
                {{ $r['persen']>=75?'Memenuhi':($r['persen']>=50?'Perhatian':'Tidak Memenuhi') }}
            </td>
        </tr>
        @endforeach
    </tbody>
</table>
<div class="footer">Sistem Presensi Wajah &nbsp;|&nbsp; {{ now()->format('Y') }}</div>
</body>
</html>
