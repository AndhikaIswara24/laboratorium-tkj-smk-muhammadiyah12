<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Efisiensi extends Model
{
    protected $table = 'efisiensi';
    protected $fillable = ['asset_id', 'metric', 'value', 'measured_at'];
}
