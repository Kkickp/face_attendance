<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class MataKuliahController extends Controller
{
    public function index(Request $request)
    {
        $query = MataKuliah::withCount('kelas');
        if ($request->filled('cari')) {
            $query->where('nama', 'like', '%' . $request->cari . '%')
                  ->orWhere('kode', 'like', '%' . $request->cari . '%');
        }
        $mataKuliah = $query->orderBy('nama')->paginate(15);
        return view('mata-kuliah.index', compact('mataKuliah'));
    }

    public function create()
    {
        return view('mata-kuliah.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'kode'           => 'required|string|max:20|unique:mata_kuliah,kode',
            'nama'           => 'required|string|max:100',
            'sks'            => 'required|integer|min:1|max:6',
            'dosen_pengampu' => 'nullable|string|max:100',
        ]);

        $mk = MataKuliah::create($request->only('kode', 'nama', 'sks', 'dosen_pengampu'));
        AuditLog::catat('Tambah Mata Kuliah', "{$request->kode} - {$request->nama}", 'MataKuliah', $mk->id);

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil ditambahkan!');
    }

    public function show(MataKuliah $mataKuliah)
    {
        $mataKuliah->load(['kelas.sesiPresensi']);
        return view('mata-kuliah.show', compact('mataKuliah'));
    }

    public function edit(MataKuliah $mataKuliah)
    {
        return view('mata-kuliah.edit', compact('mataKuliah'));
    }

    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'kode'           => 'required|string|max:20|unique:mata_kuliah,kode,' . $mataKuliah->id,
            'nama'           => 'required|string|max:100',
            'sks'            => 'required|integer|min:1|max:6',
            'dosen_pengampu' => 'nullable|string|max:100',
            'is_aktif'       => 'boolean',
        ]);

        $mataKuliah->update($request->only('kode', 'nama', 'sks', 'dosen_pengampu', 'is_aktif'));
        AuditLog::catat('Edit Mata Kuliah', "ID {$mataKuliah->id} diperbarui.", 'MataKuliah', $mataKuliah->id);

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil diperbarui!');
    }

    public function destroy(MataKuliah $mataKuliah)
    {
        $nama = $mataKuliah->nama;
        $mataKuliah->delete();
        AuditLog::catat('Hapus Mata Kuliah', "{$nama} dihapus.", 'MataKuliah', $mataKuliah->id);

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata kuliah berhasil dihapus.');
    }
}
