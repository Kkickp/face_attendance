<?php

namespace App\Http\Controllers;

use App\Models\Mahasiswa;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class MahasiswaController extends Controller
{
    public function index(Request $request)
    {
        $query = Mahasiswa::query();
        if ($request->filled('cari')) {
            $query->where('nama_lengkap', 'like', '%' . $request->cari . '%')
                  ->orWhere('nim', 'like', '%' . $request->cari . '%');
        }
        $mahasiswa = $query->orderBy('nama_lengkap')->paginate(15);
        return view('mahasiswa.index', compact('mahasiswa'));
    }

    public function create()
    {
        return view('mahasiswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nim'          => 'required|string|max:20|unique:mahasiswa,nim',
            'nama_lengkap' => 'required|string|max:100',
            'foto_base64'  => 'required|string',
        ]);

        // Kirim ke Python engine untuk enroll wajah
        $pythonUrl = env('PYTHON_ENGINE_URL', 'http://localhost:8000');
        $response  = Http::timeout(30)->post($pythonUrl . '/api/enroll', [
            'nim'          => $request->nim,
            'nama_lengkap' => $request->nama_lengkap,
            'foto_base64'  => $request->foto_base64,
        ]);

        if (!$response->successful() || $response->json('status') !== 'success') {
            return back()->withErrors(['foto' => $response->json('message', 'Gagal mendaftarkan wajah ke mesin AI.')])->withInput();
        }

        AuditLog::catat('Tambah Mahasiswa', "Mahasiswa {$request->nim} - {$request->nama_lengkap} ditambahkan.", 'Mahasiswa', $request->nim);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil didaftarkan!');
    }

    public function show(string $nim)
    {
        $mahasiswa = Mahasiswa::with(['presensi.kelas.mataKuliah'])->findOrFail($nim);
        return view('mahasiswa.show', compact('mahasiswa'));
    }

    public function edit(string $nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        return view('mahasiswa.edit', compact('mahasiswa'));
    }

    public function update(Request $request, string $nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $request->validate([
            'nama_lengkap' => 'required|string|max:100',
        ]);

        $mahasiswa->update(['nama_lengkap' => $request->nama_lengkap]);
        AuditLog::catat('Edit Mahasiswa', "Data {$nim} diubah menjadi {$request->nama_lengkap}.", 'Mahasiswa', $nim);

        return redirect()->route('mahasiswa.index')->with('success', 'Data mahasiswa berhasil diperbarui!');
    }

    public function destroy(string $nim)
    {
        $mahasiswa = Mahasiswa::findOrFail($nim);
        $nama      = $mahasiswa->nama_lengkap;
        $mahasiswa->delete();

        AuditLog::catat('Hapus Mahasiswa', "Mahasiswa {$nim} - {$nama} dihapus.", 'Mahasiswa', $nim);

        return redirect()->route('mahasiswa.index')->with('success', 'Mahasiswa berhasil dihapus.');
    }
}
