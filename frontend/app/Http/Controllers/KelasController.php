<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\MataKuliah;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class KelasController extends Controller
{
    public function index(Request $request)
    {
        $kelas = Kelas::with('mataKuliah')
            ->when($request->filled('mata_kuliah_id'), fn($q) => $q->where('mata_kuliah_id', $request->mata_kuliah_id))
            ->orderBy('hari')
            ->paginate(15);
        $mataKuliah = MataKuliah::where('is_aktif', true)->orderBy('nama')->get();
        return view('kelas.index', compact('kelas', 'mataKuliah'));
    }

    public function create()
    {
        $mataKuliah = MataKuliah::where('is_aktif', true)->orderBy('nama')->get();
        return view('kelas.create', compact('mataKuliah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'hari'           => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'      => 'required|date_format:H:i',
            'jam_selesai'    => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'        => 'nullable|string|max:50',
        ]);

        $kelas = Kelas::create($request->only('mata_kuliah_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan'));
        AuditLog::catat('Tambah Kelas', "Kelas {$request->hari} {$request->jam_mulai} ditambahkan.", 'Kelas', $kelas->id);

        return redirect()->route('kelas.index')->with('success', 'Jadwal kelas berhasil ditambahkan!');
    }

    public function show(Kelas $kela)
    {
        $kela->load(['mataKuliah', 'sesiPresensi' => fn($q) => $q->orderByDesc('tanggal')]);
        return view('kelas.show', ['kelas' => $kela]);
    }

    public function edit(Kelas $kela)
    {
        $mataKuliah = MataKuliah::where('is_aktif', true)->orderBy('nama')->get();
        return view('kelas.edit', ['kelas' => $kela, 'mataKuliah' => $mataKuliah]);
    }

    public function update(Request $request, Kelas $kela)
    {
        $request->validate([
            'mata_kuliah_id' => 'required|exists:mata_kuliah,id',
            'hari'           => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu',
            'jam_mulai'      => 'required|date_format:H:i',
            'jam_selesai'    => 'required|date_format:H:i|after:jam_mulai',
            'ruangan'        => 'nullable|string|max:50',
        ]);

        $kela->update($request->only('mata_kuliah_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan', 'is_aktif'));
        AuditLog::catat('Edit Kelas', "Kelas ID {$kela->id} diperbarui.", 'Kelas', $kela->id);

        return redirect()->route('kelas.index')->with('success', 'Jadwal kelas berhasil diperbarui!');
    }

    public function destroy(Kelas $kela)
    {
        $kela->delete();
        AuditLog::catat('Hapus Kelas', "Kelas ID {$kela->id} dihapus.", 'Kelas', $kela->id);
        return redirect()->route('kelas.index')->with('success', 'Jadwal kelas berhasil dihapus.');
    }
}
