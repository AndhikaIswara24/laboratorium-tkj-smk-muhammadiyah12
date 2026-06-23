<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    
    public function index()
    {
        $users = User::orderBy('created_at','desc')->paginate(20);
        return view('admin.users.index', compact('users'));
    }

    public function edit(User $user)
    {
        $roles = ['admin','teknisi','user'];
        return view('admin.users.edit', compact('user','roles'));
    }

    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'role' => ['required','in:admin,teknisi,user'],
        ]);

        $user->role = $data['role'];
        $user->save();

        return redirect()->route('admin.users.index')->with('status', 'Role updated.');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return redirect()->back()->with('status', 'Cannot delete yourself.');
        }
        $user->delete();
        return redirect()->route('admin.users.index')->with('status', 'User deleted.');
    }
}
