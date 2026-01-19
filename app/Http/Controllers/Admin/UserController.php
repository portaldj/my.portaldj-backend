<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Http\Request;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles')->latest();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        return Inertia::render('Admin/Users/Index', [
            'users' => $query->paginate(10)->withQueryString(),
            'filters' => $request->only(['search']),
        ]);
    }

    public function create()
    {
        return Inertia::render('Admin/Users/Create', [
            'roles' => Role::all(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'username' => 'required|string|max:255|unique:profiles,username',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'email_verified_at' => now(), // Auto-verify admin created users? Usually yes.
        ]);

        // Create Profile
        $user->profile()->create([
            'username' => $validated['username'],
        ]);

        if (isset($validated['roles'])) {
            $user->assignRole($validated['roles']);
        } else {
            $user->assignRole('User');
        }

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }

    public function edit(User $user)
    {
        return Inertia::render('Admin/Users/Edit', [
            'user' => $user->load('roles', 'profile'),
            'roles' => Role::all(),
        ]);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', 'unique:profiles,username,' . ($user->profile->id ?? 'NULL')], // Check profile table
            'roles' => 'array',
            'roles.*' => 'exists:roles,name',
            'banned_until' => 'nullable|date',
            'ban_reason' => 'nullable|string|max:255',
        ]);

        // Update Profile Username
        if (isset($validated['username']) && $user->profile) {
            $user->profile->update(['username' => $validated['username']]);
        }

        // Update Roles
        if (isset($validated['roles'])) {
            $user->syncRoles($validated['roles']);
        }

        // Update Ban Status
        if ($request->has('banned_until')) {
            $user->banned_until = $validated['banned_until'];
            $user->ban_reason = $validated['ban_reason'] ?? null;
            $user->save();
        }

        return redirect()->route('admin.users.index')->with('success', 'User updated successfully.');
    }

    public function destroy(User $user)
    {
        if ($user->hasRole('Admin') && User::role('Admin')->count() === 1) {
            return redirect()->back()->with('error', 'Cannot delete the last admin.');
        }

        $user->delete();
        return redirect()->route('admin.users.index')->with('success', 'User deleted.');
    }
}
