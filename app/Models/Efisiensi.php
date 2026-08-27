<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Efisiensi extends Model
{
    use HasFactory;

    protected $table = 't_efisiensi';
    protected $primaryKey = 'id_efisiensi';
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'id_aset',
        'tgl_observasi',
        'jam_ops',
        'penggunaan',
        'jml_user',
        'downtime',
        'perform',
        'umur_ekonomis',
        'efi_out',
    ];

    protected $casts = [
        'tgl_observasi' => 'date',
        'jam_ops' => 'float',
        'jml_user' => 'integer',
        'downtime' => 'float',
        'umur_ekonomis' => 'integer',
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
