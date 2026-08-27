<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilPrediksi extends Model
{
    use HasFactory;

    protected $table = 't_hasil_prediksi';
    protected $primaryKey = 'id_prediksi';
    public $timestamps = false;

    protected $fillable = [
        'id_dataset',
        'id_aset',
        'tgl_prediksi',
        'hasil_prediksi',
        'prob_layak',
        'prob_servis',
        'prob_tidak_layak',
    ];

    protected $casts = [
        'tgl_prediksi' => 'datetime',
        'prob_layak' => 'float',
        'prob_servis' => 'float',
        'prob_tidak_layak' => 'float',
    ];

    public function dataset()
    {
        return $this->belongsTo(NaiveBayesDataset::class, 'id_dataset', 'id_dataset');
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class, 'id_aset', 'id_aset');
    }
}
