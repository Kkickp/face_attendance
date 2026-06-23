<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalMahasiswa  = Mahasiswa::count();
        $totalMataKuliah = MataKuliah::where('is_aktif', true)->count();
        $presensiHariIni = Presensi::whereDate('waktu_presensi', today())->count();
        $sesiAktif       = SesiPresensi::where('status', 'buka')->where('tanggal', today())->count();

        // Grafik 7 hari terakhir
        $grafik = [];
        for ($i = 6; $i >= 0; $i--) {
            $tgl = now()->subDays($i)->toDateString();
            $grafik[] = [
                'tanggal' => now()->subDays($i)->format('d/m'),
                'jumlah'  => Presensi::whereDate('waktu_presensi', $tgl)->count(),
            ];
        }

        // Log presensi terbaru
        $presensiTerbaru = Presensi::with(['mahasiswa', 'kelas.mataKuliah'])
            ->orderByDesc('waktu_presensi')
            ->limit(10)
            ->get();

        // Cek status Python engine
        $pythonUrl  = env('PYTHON_ENGINE_URL', 'http://localhost:8000');
        $pythonAktif = false;
        try {
            $ctx = stream_context_create(['http' => ['timeout' => 2]]);
            $res = @file_get_contents($pythonUrl . '/docs', false, $ctx);
            $pythonAktif = $res !== false;
        } catch (\Exception $e) {}

        return view('dashboard.index', compact(
            'totalMahasiswa', 'totalMataKuliah', 'presensiHariIni',
            'sesiAktif', 'grafik', 'presensiTerbaru', 'pythonAktif'
        ));
    }
}
