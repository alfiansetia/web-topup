<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class SetPasswordController extends Controller
{
    public function show()
    {
        return Inertia::render('Auth/SetPassword');
    }

    public function store(Request $request)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();
        $user->update([
            'password' => bcrypt($request->password),
        ]);

        return redirect()->route('dashboard.index')->with('success', 'Password berhasil diatur! Sekarang Anda bisa login tanpa Google.');
    }
}
