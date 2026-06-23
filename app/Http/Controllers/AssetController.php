<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Asset;

class AssetController extends Controller
{
    public function index()
    {
        $items = Asset::latest()->limit(50)->get();
        return view('assets.index', compact('items'));
    }
}
