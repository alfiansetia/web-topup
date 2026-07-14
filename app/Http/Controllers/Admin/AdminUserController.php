<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AdminUserController extends Controller
{
    public function index(Request $request): Response
    {
        $query = User::query();

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($role = $request->input('role')) {
            $query->where('role', $role);
        }

        if ($request->input('blocked') === '1') {
            $query->where('is_blocked', true);
        } elseif ($request->input('blocked') === '0') {
            $query->where('is_blocked', false);
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => $request->only(['search', 'role', 'blocked']),
        ]);
    }

    public function toggleBlock(User $user): RedirectResponse
    {
        // Prevent blocking self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa memblokir diri sendiri.');
        }

        $user->update(['is_blocked' => !$user->is_blocked]);

        $status = $user->is_blocked ? 'diblokir' : 'diaktifkan';

        return back()->with('success', "User {$user->name} berhasil {$status}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        // Prevent deleting self
        if ($user->id === auth()->id()) {
            return back()->with('error', 'Anda tidak bisa menghapus diri sendiri.');
        }

        // Prevent deleting admin users
        if ($user->role === 'admin') {
            return back()->with('error', 'Tidak bisa menghapus akun admin.');
        }

        $user->delete();

        return back()->with('success', "User {$user->name} berhasil dihapus.");
    }
}
