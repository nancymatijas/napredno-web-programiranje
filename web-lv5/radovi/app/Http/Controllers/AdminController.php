<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }


public function index()
{
    if (Auth::user()->role !== 'admin') {
        abort(403);
    }

    $userCounts = [
        'admin' => User::where('role', 'admin')->count(),
        'nastavnik' => User::where('role', 'nastavnik')->count(),
        'student' => User::where('role', 'student')->count()
    ];

    $latestUsers = User::latest()->take(5)->get();

    return view('admin.dashboard', compact('userCounts', 'latestUsers'));
}

    
    public function users() {
        if (Auth::user()->role !== 'admin') abort(403);
        $users = User::where('role', '!=', 'admin')->get();
        return view('admin.users', compact('users'));
    }

    public function updateRole(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Nemate ovlasti za ovu akciju.');
        }
    
        $request->validate([
            'role' => 'required|in:nastavnik,student',
        ]);
    
        $user->role = $request->role;
        $user->save(); 
    
        return back()->with('success', 'Uloga uspješno promijenjena.');
    }
    
}
