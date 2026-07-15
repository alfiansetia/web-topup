<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
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

    public function create(): Response
    {
        return Inertia::render('Admin/Users/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email',
            'password'    => 'required|string|min:8|confirmed',
            'role'        => 'required|in:admin,user',
            'telegram_id' => 'nullable|string|max:50',
        ]);

        User::create([
            'name'              => $request->name,
            'email'             => $request->email,
            'password'          => Hash::make($request->password),
            'role'              => $request->role,
            'telegram_id'       => $request->telegram_id ?: null,
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')->with('success', "User {$request->name} berhasil ditambahkan.");
    }

    public function edit(User $user): Response
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->only(['id', 'name', 'email', 'role', 'is_blocked', 'telegram_id']),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $request->validate([
            'name'        => 'required|string|max:255',
            'email'       => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'role'        => 'required|in:admin,user',
            'is_blocked'  => 'required|boolean',
            'telegram_id' => 'nullable|string|max:50',
        ]);

        // Prevent removing own admin role
        if ($user->id === auth()->id() && $request->role !== 'admin') {
            return back()->with('error', 'Anda tidak bisa mengubah role diri sendiri.');
        }

        $user->update([
            'name'        => $request->name,
            'email'       => $request->email,
            'role'        => $request->role,
            'is_blocked'  => $request->is_blocked,
            'telegram_id' => $request->telegram_id ?: null,
        ]);

        return redirect()->route('admin.users.index')->with('success', "User {$user->name} berhasil diperbarui.");
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
