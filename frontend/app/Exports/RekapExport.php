<?php

namespace App\Exports;

use App\Models\Mahasiswa;
use App\Models\MataKuliah;
use App\Models\Presensi;
use App\Models\SesiPresensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapExport implements FromCollection, WithHeadings, WithMapping, WithTitle
{
    private $mk;
    private int $totalSesi;

    public function __construct(private int $mkId) {
        $this->mk       = MataKuliah::with('kelas')->findOrFail($mkId);
        $this->totalSesi = SesiPresensi::whereIn('kelas_id', $this->mk->kelas->pluck('id'))->count();
    }

    public function collection()
    {
        return Mahasiswa::all()->map(function ($mhs) {
            $hadir  = Presensi::where('nim', $mhs->nim)->whereIn('kelas_id', $this->mk->kelas->pluck('id'))->where('status', 'Hadir')->count();
            $persen = $this->totalSesi > 0 ? round(($hadir / $this->totalSesi) * 100, 1) : 0;
            return collect(['nim' => $mhs->nim, 'nama' => $mhs->nama_lengkap, 'hadir' => $hadir, 'total_sesi' => $this->totalSesi, 'persen' => $persen]);
        })->sortByDesc('persen')->values();
    }

    public function headings(): array
    {
        return ['No', 'NIM', 'Nama Mahasiswa', 'Hadir', 'Total Sesi', '% Kehadiran', 'Keterangan'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row['nim'],
            $row['nama'],
            $row['hadir'],
            $row['total_sesi'],
            $row['persen'] . '%',
            $row['persen'] >= 75 ? 'Memenuhi' : 'Tidak Memenuhi',
        ];
    }

    public function title(): string
    {
        return 'Rekap ' . $this->mk->nama;
    }
}
