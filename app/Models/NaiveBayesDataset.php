<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NaiveBayesDataset extends Model
{
    protected $table = 't_naive_bayes_dataset';
    protected $primaryKey = 'id_dataset';
    public $timestamps = false;

    protected $fillable = [
        'id_aset',
        'kondisi_brg',
        'usia_pakai',
        'frq_kerusakan',
        'jenis_pm',
        'interval_pm',
        'efi_out',
        'downtime',
        'lingkungan',
        'daya_listrik',
        'sparepart',
        'kelas_label',
        'tgl_input',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
