<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VariabelEksternal;

class VariabelEksternalController extends Controller
{
    public function index()
    {
        $rows = VariabelEksternal::latest()->limit(50)->get();
        return view('variabel-eksternal.index', compact('rows'));
    }
}
