<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\SesiPresensi;
use App\Models\AuditLog;
use App\Notifications\PresensiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SesiPresensiController extends Controller
{
    public function index()
    {
        $sesi = SesiPresensi::with(['kelas.mataKuliah', 'dibukaDari'])
            ->orderByDesc('tanggal')
            ->paginate(20);
        $kelasAktif = Kelas::with('mataKuliah')->where('is_aktif', true)->get();
        return view('sesi.index', compact('sesi', 'kelasAktif'));
    }

    public function buka(Request $request)
    {
        $request->validate([
            'kelas_id' => 'required|exists:kelas,id',
        ]);

        // Cek apakah sudah ada sesi terbuka hari ini untuk kelas ini
        $existing = SesiPresensi::where('kelas_id', $request->kelas_id)
            ->where('tanggal', today())
            ->where('status', 'buka')
            ->first();

        if ($existing) {
            return back()->withErrors(['kelas_id' => 'Sesi presensi untuk kelas ini sudah dibuka hari ini.']);
        }

        $sesi = SesiPresensi::create([
            'kelas_id'    => $request->kelas_id,
            'tanggal'     => today(),
            'status'      => 'buka',
            'dibuka_oleh' => Auth::id(),
            'dibuka_pada' => now(),
        ]);

        $kelas = Kelas::with('mataKuliah')->find($request->kelas_id);
        AuditLog::catat('Buka Sesi Presensi', "Sesi {$kelas->mataKuliah->nama} ({$kelas->hari} {$kelas->jam_mulai}) dibuka.", 'SesiPresensi', $sesi->id);

        // Kirim notifikasi ke admin
        Auth::user()->notify(new PresensiNotification(
            'Sesi Dibuka',
            "Sesi presensi {$kelas->mataKuliah->nama} ({$kelas->hari}) berhasil dibuka."
        ));

        return back()->with('success', 'Sesi presensi berhasil dibuka!');
    }

    public function tutup(SesiPresensi $sesi)
    {
        $sesi->update(['status' => 'tutup', 'ditutup_pada' => now()]);

        $kelas = $sesi->kelas->load('mataKuliah');
        AuditLog::catat('Tutup Sesi Presensi', "Sesi {$kelas->mataKuliah->nama} ditutup.", 'SesiPresensi', $sesi->id);

        return back()->with('success', 'Sesi presensi berhasil ditutup.');
    }
}
