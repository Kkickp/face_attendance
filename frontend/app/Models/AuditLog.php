<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $table = 'audit_logs';
    protected $fillable = ['user_id', 'aksi', 'model', 'model_id', 'keterangan', 'ip_address'];

    public $timestamps = true;
    const UPDATED_AT = null;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function catat(string $aksi, string $keterangan = null, string $model = null, string $modelId = null): void
    {
        static::create([
            'user_id'     => auth()->id(),
            'aksi'        => $aksi,
            'model'       => $model,
            'model_id'    => $modelId,
            'keterangan'  => $keterangan,
            'ip_address'  => request()->ip(),
        ]);
    }
}
