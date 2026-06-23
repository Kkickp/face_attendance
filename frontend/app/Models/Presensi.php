<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Presensi extends Model
{
    protected $table = 'presensi';
    public $timestamps = false;

    protected $fillable = ['nim', 'kelas_id', 'sesi_id', 'waktu_presensi', 'status', 'foto_bukti'];

    protected $casts = [
        'waktu_presensi' => 'datetime',
    ];

    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'nim', 'nim');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function sesi()
    {
        return $this->belongsTo(SesiPresensi::class, 'sesi_id');
    }
}
