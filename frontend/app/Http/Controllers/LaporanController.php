<?php

namespace App\Http\Controllers;

use App\Models\Presensi;
use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Kelas;
use App\Models\AuditLog;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LaporanExport;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $query = Presensi::with(['mahasiswa', 'kelas.mataKuliah'])
            ->orderByDesc('waktu_presensi');

        if ($request->filled('tanggal_dari')) {
            $query->whereDate('waktu_presensi', '>=', $request->tanggal_dari);
        }
        if ($request->filled('tanggal_sampai')) {
            $query->whereDate('waktu_presensi', '<=', $request->tanggal_sampai);
        }
        if ($request->filled('mata_kuliah_id')) {
            $query->whereHas('kelas', fn($q) => $q->where('mata_kuliah_id', $request->mata_kuliah_id));
        }
        if ($request->filled('nim')) {
            $query->where('nim', 'like', '%' . $request->nim . '%');
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $presensi   = $query->paginate(20)->withQueryString();
        $mataKuliah = MataKuliah::orderBy('nama')->get();

        return view('laporan.index', compact('presensi', 'mataKuliah'));
    }

    public function exportPdf(Request $request)
    {
        $query = Presensi::with(['mahasiswa', 'kelas.mataKuliah'])->orderByDesc('waktu_presensi');
        $this->applyFilters($query, $request);
        $presensi = $query->get();

        AuditLog::catat('Export PDF Laporan', 'Laporan presensi diexport ke PDF.');
        $pdf = Pdf::loadView('laporan.pdf', compact('presensi'))->setPaper('a4', 'landscape');
        return $pdf->download('laporan-presensi-' . now()->format('Ymd') . '.pdf');
    }

    public function exportExcel(Request $request)
    {
        AuditLog::catat('Export Excel Laporan', 'Laporan presensi diexport ke Excel.');
        return Excel::download(new LaporanExport($request->all()), 'laporan-presensi-' . now()->format('Ymd') . '.xlsx');
    }

    private function applyFilters($query, $request)
    {
        if ($request->filled('tanggal_dari'))   $query->whereDate('waktu_presensi', '>=', $request->tanggal_dari);
        if ($request->filled('tanggal_sampai')) $query->whereDate('waktu_presensi', '<=', $request->tanggal_sampai);
        if ($request->filled('mata_kuliah_id')) $query->whereHas('kelas', fn($q) => $q->where('mata_kuliah_id', $request->mata_kuliah_id));
        if ($request->filled('nim'))            $query->where('nim', 'like', '%' . $request->nim . '%');
        if ($request->filled('status'))         $query->where('status', $request->status);
    }
}
