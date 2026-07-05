<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    use HasFactory;

    protected $table = 't_pemeliharaan';
    protected $primaryKey = 'id_pm';
    public $timestamps = false;

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
    ];

    /**
     * Relasi ke tabel t_aset
     */
    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
