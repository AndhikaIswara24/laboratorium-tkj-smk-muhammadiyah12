<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $table = 't_aset';
    protected $primaryKey = 'id_aset';
    public $timestamps = true;
    
    protected $fillable = [
        'kode_brg',
        'nama_brg',
        'merk_tipe',
        'spesifikasi',
        'lokasi',
        'thn_perolehan',
        'harga_perolehan',
        'asal_usul'
    ];

    protected $casts = [
        'harga_perolehan' => 'decimal:2',
    ];
}
