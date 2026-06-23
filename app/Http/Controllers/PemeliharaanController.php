<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Pemeliharaan;

class PemeliharaanController extends Controller
{
    public function index()
    {
        $rows = Pemeliharaan::latest()->limit(50)->get();
        return view('pemeliharaan.index', compact('rows'));
    }
}
