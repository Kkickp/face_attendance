<?php

namespace App\Exports;

use App\Models\Presensi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LaporanExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private array $filters = []) {}

    public function collection()
    {
        $query = Presensi::with(['mahasiswa', 'kelas.mataKuliah'])->orderByDesc('waktu_presensi');
        if (!empty($this->filters['tanggal_dari']))   $query->whereDate('waktu_presensi', '>=', $this->filters['tanggal_dari']);
        if (!empty($this->filters['tanggal_sampai'])) $query->whereDate('waktu_presensi', '<=', $this->filters['tanggal_sampai']);
        if (!empty($this->filters['mata_kuliah_id'])) $query->whereHas('kelas', fn($q) => $q->where('mata_kuliah_id', $this->filters['mata_kuliah_id']));
        if (!empty($this->filters['nim']))            $query->where('nim', 'like', '%' . $this->filters['nim'] . '%');
        if (!empty($this->filters['status']))         $query->where('status', $this->filters['status']);
        return $query->get();
    }

    public function headings(): array
    {
        return ['No', 'NIM', 'Nama Mahasiswa', 'Mata Kuliah', 'Hari/Kelas', 'Waktu Presensi', 'Status'];
    }

    public function map($row): array
    {
        static $no = 0;
        $no++;
        return [
            $no,
            $row->nim,
            $row->mahasiswa->nama_lengkap ?? '-',
            $row->kelas->mataKuliah->nama ?? '-',
            ($row->kelas->hari ?? '') . ' ' . ($row->kelas->jam_mulai ?? ''),
            $row->waktu_presensi?->format('d/m/Y H:i:s'),
            $row->status,
        ];
    }
}
