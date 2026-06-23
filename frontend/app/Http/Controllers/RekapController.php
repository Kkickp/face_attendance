<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;
use Illuminate\Http\Request;

class RekapController extends Controller
{
    public function index(Request $request)
    {
        $mataKuliah = MataKuliah::orderBy('nama')->get();
        $rekap      = collect();

        if ($request->filled('mata_kuliah_id')) {
            $mk    = MataKuliah::with('kelas')->findOrFail($request->mata_kuliah_id);
            $kelasList = $mk->kelas;

            // Total sesi per kelas
            $totalSesi = SesiPresensi::whereIn('kelas_id', $kelasList->pluck('id'))->count();

            $rekap = Mahasiswa::all()->map(function ($mhs) use ($kelasList, $totalSesi, $mk) {
                $hadir = Presensi::where('nim', $mhs->nim)
                    ->whereIn('kelas_id', $kelasList->pluck('id'))
                    ->where('status', 'Hadir')
                    ->count();
                $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0;
                return [
                    'nim'         => $mhs->nim,
                    'nama'        => $mhs->nama_lengkap,
                    'hadir'       => $hadir,
                    'total_sesi'  => $totalSesi,
                    'persen'      => $persen,
                    'badge'       => $persen >= 75 ? 'success' : ($persen >= 50 ? 'warning' : 'danger'),
                    'mk_nama'     => $mk->nama,
                ];
            })->sortByDesc('persen')->values();
        }

        return view('rekap.index', compact('mataKuliah', 'rekap'));
    }

    public function exportPdf(Request $request)
    {
        $data = $this->buildRekapData($request->mata_kuliah_id);
        $pdf  = Pdf::loadView('rekap.pdf', $data)->setPaper('a4', 'portrait');
        return $pdf->download('rekap-kehadiran-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        return Excel::download(new RekapExport($request->mata_kuliah_id), 'rekap-kehadiran-' . now()->format('Ymd') . '.xlsx');
    }

    private function buildRekapData($mkId)
    {
        $mk        = MataKuliah::with('kelas')->findOrFail($mkId);
        $kelasList = $mk->kelas;
        $totalSesi = SesiPresensi::whereIn('kelas_id', $kelasList->pluck('id'))->count();
        $rekap     = Mahasiswa::all()->map(function ($mhs) use ($kelasList, $totalSesi) {
            $hadir  = Presensi::where('nim', $mhs->nim)->whereIn('kelas_id', $kelasList->pluck('id'))->where('status', 'Hadir')->count();
            $persen = $totalSesi > 0 ? round(($hadir / $totalSesi) * 100, 1) : 0;
            return ['nim' => $mhs->nim, 'nama' => $mhs->nama_lengkap, 'hadir' => $hadir, 'total_sesi' => $totalSesi, 'persen' => $persen];
        })->sortByDesc('persen')->values();
        return compact('mk', 'rekap', 'totalSesi');
    }
}
