<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $table = 't_aset';
    protected $primaryKey = 'id_aset';
    public $timestamps = true;
    const UPDATED_AT = null; // t_aset hanya punya kolom created_at

    
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

    public function variabelEksternal()
    {
        return $this->hasMany(VariabelEksternal::class, 'id_aset', 'id_aset');
    }

    public function kondisiFisik()
    {
        return $this->hasMany(KondisiFisik::class, 'id_aset', 'id_aset');
    }

    public function pemeliharaan()
    {
        return $this->hasMany(Pemeliharaan::class, 'id_aset', 'id_aset');
    }

    public function efisiensi()
    {
        return $this->hasMany(Efisiensi::class, 'id_aset', 'id_aset');
    }
}
