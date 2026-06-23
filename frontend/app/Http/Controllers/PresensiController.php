<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\SesiPresensi;
use App\Models\Kelas;
use App\Models\User;
use App\Notifications\PresensiNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class PresensiController extends Controller
{
    /**
     * Halaman utama presensi (publik)
     */
    public function index()
    {
        // Ambil semua sesi yang sedang terbuka hari ini
        $sesiAktif = SesiPresensi::with(['kelas.mataKuliah'])
            ->where('tanggal', today())
            ->where('status', 'buka')
            ->get();

        return view('presensi.index', compact('sesiAktif'));
    }

    /**
     * Proses presensi via foto (dipanggil dari JS)
     */
    public function proses(Request $request)
    {
        $request->validate([
            'foto_base64' => 'required|string',
            'sesi_id'     => 'required|exists:sesi_presensi,id',
        ]);

        // Validasi sesi masih terbuka
        $sesi = SesiPresensi::with('kelas.mataKuliah')->find($request->sesi_id);
        if (!$sesi || $sesi->status !== 'buka') {
            return response()->json(['status' => 'error', 'message' => 'Sesi presensi sudah ditutup.'], 400);
        }

        // Kirim ke Python engine
        $pythonUrl = env('PYTHON_ENGINE_URL', 'http://localhost:8000');
        try {
            $response = Http::timeout(15)->post($pythonUrl . '/api/recognize', [
                'foto_base64' => $request->foto_base64,
            ]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Mesin AI tidak dapat dihubungi.'], 503);
        }

        $data = $response->json();

        if ($data['status'] === 'success') {
            $nim  = $data['nim'];
            $nama = $data['nama'];

            // Cek apakah sudah presensi di sesi ini
            $sudahPresensi = Presensi::where('nim', $nim)->where('sesi_id', $sesi->id)->exists();
            if ($sudahPresensi) {
                return response()->json([
                    'status'  => 'warning',
                    'message' => "Halo {$nama}! Anda sudah melakukan presensi di sesi ini.",
                ]);
            }

            // Simpan presensi
            Presensi::create([
                'nim'            => $nim,
                'kelas_id'       => $sesi->kelas_id,
                'sesi_id'        => $sesi->id,
                'waktu_presensi' => now(),
                'status'         => 'Hadir',
                'foto_bukti'     => $request->foto_base64,
            ]);

            // Kirim notifikasi ke semua admin
            $matkul = $sesi->kelas->mataKuliah->nama ?? '-';
            foreach (User::all() as $admin) {
                $admin->notify(new PresensiNotification(
                    'Presensi Berhasil',
                    "{$nama} ({$nim}) hadir di {$matkul}."
                ));
            }

            return response()->json([
                'status'  => 'success',
                'message' => "✅ Presensi Berhasil! Halo {$nama}!",
                'nama'    => $nama,
                'nim'     => $nim,
            ]);
        }

        return response()->json([
            'status'  => $data['status'] ?? 'error',
            'message' => $data['message'] ?? 'Wajah tidak dikenali.',
        ]);
    }
}
