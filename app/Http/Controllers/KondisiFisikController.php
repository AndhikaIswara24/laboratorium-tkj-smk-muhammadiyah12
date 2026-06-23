<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KondisiFisik;

class KondisiFisikController extends Controller
{
    public function index()
    {
        $rows = KondisiFisik::latest()->limit(50)->get();
        return view('kondisi-fisik.index', compact('rows'));
    }
}
