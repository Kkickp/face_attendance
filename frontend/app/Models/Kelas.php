<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['mata_kuliah_id', 'hari', 'jam_mulai', 'jam_selesai', 'ruangan', 'is_aktif'];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    public function mataKuliah()
    {
        return $this->belongsTo(MataKuliah::class, 'mata_kuliah_id');
    }

    public function sesiPresensi()
    {
        return $this->hasMany(SesiPresensi::class, 'kelas_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'kelas_id');
    }

    public function sesiAktifHariIni()
    {
        return $this->sesiPresensi()
            ->where('tanggal', today())
            ->where('status', 'buka')
            ->first();
    }
}
