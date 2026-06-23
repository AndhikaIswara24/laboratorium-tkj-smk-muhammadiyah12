<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        if (!$user) {
            return redirect()->route('login');
        }

        if ($user->role === 'admin') {
            return view('dashboard.admin');
        }

        if ($user->role === 'teknisi') {
            return view('dashboard.teknisi');
        }

        return view('dashboard.user');
    }
}
