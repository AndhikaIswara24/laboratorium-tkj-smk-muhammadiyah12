<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pemeliharaan extends Model
{
    protected $table = 'pemeliharaan';
    protected $fillable = ['asset_id', 'performed_at', 'performed_by', 'notes'];
}
