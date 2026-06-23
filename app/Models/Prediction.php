<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Prediction extends Model
{
    protected $table = 'predictions';
    protected $fillable = ['asset_id', 'predicted_label', 'probability', 'created_at'];
}
