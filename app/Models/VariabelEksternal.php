<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariabelEksternal extends Model
{
    use HasFactory;

    protected $table = 't_variabel_eksternal';
    protected $primaryKey = 'id_eksternal';
    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'id_aset',
        'tgl_observasi',
        'lingkungan',
        'daya_listrik',
        'sparepart',
        'anggaran',
        'ext_effect',
    ];

    protected $casts = [
        'tgl_observasi' => 'date',
        'created_at' => 'datetime',
    ];

    /**
     * Scope: only records created within the last 24 hours.
     */
    public function scopeRecent($query)
    {
        return $query->where('created_at', '>=', now()->subHours(24));
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
