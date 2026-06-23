<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Prediction;

class PrediksiController extends Controller
{
    public function index()
    {
        // placeholder for Naive Bayes prediction UI
        return view('prediksi.index');
    }
}
