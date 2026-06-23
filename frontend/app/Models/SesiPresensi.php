<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SesiPresensi extends Model
{
    protected $table = 'sesi_presensi';
    protected $fillable = ['kelas_id', 'tanggal', 'status', 'dibuka_oleh', 'dibuka_pada', 'ditutup_pada'];

    protected $casts = [
        'tanggal'     => 'date',
        'dibuka_pada' => 'datetime',
        'ditutup_pada'=> 'datetime',
    ];

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function dibukaDari()
    {
        return $this->belongsTo(User::class, 'dibuka_oleh');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'sesi_id');
    }

    public function isAktif(): bool
    {
        return $this->status === 'buka';
    }
}
