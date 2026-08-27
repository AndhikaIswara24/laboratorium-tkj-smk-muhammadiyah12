<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $table = 't_pemeliharaan';
    protected $primaryKey = 'id_pm';
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'id_aset',
        'tgl_pm',
        'jenis_pm',
        'interval_bulan',
        'pelaksana',
        'biaya_servis',
        'kon_after',
        'ket_pm',
    ];

    protected $casts = [
        'tgl_pm' => 'date',
        'interval_bulan' => 'integer',
        'biaya_servis' => 'decimal:2',
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
