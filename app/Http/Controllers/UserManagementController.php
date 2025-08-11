<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        // Filter by role
        if ($request->filled('role') && $request->role !== 'all') {
            $query->where('role', $request->role);
        }

        // Filter by status
        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        // Sort functionality
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $users = $query->paginate(10)->withQueryString();

        return view('admin.user_management', compact('users'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,staff,user'],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
        ]);

        ActivityService::logUserCreated($user->id, $user->name, $user->role);

        return redirect()->route('admin.user_management')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,staff,user'],
        ]);

        $oldRole = $user->role;
        $changes = [
            'name' => $request->name !== $user->name ? ['old' => $user->name, 'new' => $request->name] : null,
            'email' => $request->email !== $user->email ? ['old' => $user->email, 'new' => $request->email] : null,
            'role' => $request->role !== $user->role ? ['old' => $user->role, 'new' => $request->role] : null,
        ];

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'role' => $request->role,
        ]);

        ActivityService::logUserUpdated($user->id, $user->name, $changes);

        if ($oldRole !== $user->role) {
            ActivityService::logUserRoleChanged($user->id, $user->name, $oldRole, $user->role);
        }

        return redirect()->route('admin.user_management')->with('success', 'User updated successfully.');
    }

    public function toggleStatus(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->route('admin.user_management')->with('error', 'You cannot deactivate your own account.');
        }

        $oldStatus = $user->email_verified_at ? 'active' : 'inactive';

        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);

        $user->refresh();

        $newStatus = $user->email_verified_at ? 'active' : 'inactive';

        ActivityService::logUserStatusChanged($user->id, $user->name, $oldStatus, $newStatus);

        $status = $user->email_verified_at ? 'activated' : 'deactivated';
        return redirect()->route('admin.user_management')->with('success', "User {$status} successfully.");
    }
}
