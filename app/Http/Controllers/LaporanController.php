<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Laporan;

class LaporanController extends Controller
{
    public function index()
    {
        $rows = Laporan::latest()->limit(50)->get();
        return view('laporan.index', compact('rows'));
    }
}
