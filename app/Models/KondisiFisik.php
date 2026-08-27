<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KondisiFisik extends Model
{
    use HasFactory;

    protected $table = 't_kondisi_fisik';
    protected $primaryKey = 'id_kondisi';
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'id_aset',
        'tgl_observasi',
        'kondisi_brg',
        'ket_teknis',
        'usia_pakai',
        'frq_kerusakan',
        'kelas_label',
    ];

    protected $casts = [
        'tgl_observasi' => 'date',
        'usia_pakai' => 'integer',
        'frq_kerusakan' => 'integer',
        'created_at' => 'datetime',
    ];

    /**
     * Scope: only records created within the last 24 hours.
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subHours(24));
    }

    /**
     * Relasi ke tabel t_aset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
