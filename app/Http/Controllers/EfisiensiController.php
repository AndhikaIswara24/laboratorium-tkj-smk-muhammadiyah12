<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Efisiensi;

class EfisiensiController extends Controller
{
    public function index()
    {
        $rows = Efisiensi::latest()->limit(50)->get();
        return view('efisiensi.index', compact('rows'));
    }
}
