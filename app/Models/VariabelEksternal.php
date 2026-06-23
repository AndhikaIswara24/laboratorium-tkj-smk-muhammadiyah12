<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VariabelEksternal extends Model
{
    protected $table = 'variabel_eksternal';
    protected $fillable = ['name', 'value', 'recorded_at'];
}
