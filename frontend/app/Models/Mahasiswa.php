<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Mahasiswa extends Model
{
    protected $table = 'mahasiswa';
    protected $primaryKey = 'nim';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['nim', 'nama_lengkap', 'face_encoding'];

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'nim', 'nim');
    }

    public function rekapKehadiran($kelasId = null)
    {
        $query = $this->presensi()->where('status', 'Hadir');
        if ($kelasId) {
            $query->where('kelas_id', $kelasId);
        }
        return $query->count();
    }
}
