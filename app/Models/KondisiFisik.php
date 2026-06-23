<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class KondisiFisik extends Model
{
    protected $table = 'kondisi_fisik';
    protected $fillable = ['asset_id', 'status', 'checked_at', 'notes'];
}
