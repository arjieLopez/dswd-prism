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
                $q->where(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$search}%")
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

        if ($sortBy === 'name') {
            $query->orderByRaw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) $sortOrder");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->paginate(10)->withQueryString();

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.user_management', compact('users', 'recentActivities'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'in:admin,staff,user'],
            'designation' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'designation' => $request->designation,
            'employee_id' => $request->employee_id,
            'office' => $request->office,
        ]);

        ActivityService::logUserCreated($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name, $user->role);

        return redirect()->route('admin.user_management')->with('success', 'User created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'role' => ['required', 'string', 'in:admin,staff,user'],
            'designation' => ['nullable', 'string', 'max:255'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'max:255'],
        ]);

        $oldRole = $user->role;
        $changes = [
            'first_name'    => $request->first_name !== $user->first_name ? ['old' => $user->first_name, 'new' => $request->first_name] : null,
            'middle_name'   => $request->middle_name !== $user->middle_name ? ['old' => $user->middle_name, 'new' => $request->middle_name] : null,
            'last_name'     => $request->last_name !== $user->last_name ? ['old' => $user->last_name, 'new' => $request->last_name] : null,
            'email'         => $request->email !== $user->email ? ['old' => $user->email, 'new' => $request->email] : null,
            'role'          => $request->role !== $user->role ? ['old' => $user->role, 'new' => $request->role] : null,
            'designation'   => $request->designation !== $user->designation ? ['old' => $user->designation, 'new' => $request->designation] : null,
            'employee_id'   => $request->employee_id !== $user->employee_id ? ['old' => $user->employee_id, 'new' => $request->employee_id] : null,
            'office'        => $request->office !== $user->office ? ['old' => $user->office, 'new' => $request->office] : null,
        ];

        $user->update([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'role' => $request->role,
            'designation' => $request->designation,
            'employee_id' => $request->employee_id,
            'office' => $request->office,
        ]);

        ActivityService::logUserUpdated($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name, $changes);

        if ($oldRole !== $user->role) {
            ActivityService::logUserRoleChanged($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name, $oldRole, $user->role);
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

        ActivityService::logUserStatusChanged($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name, $oldStatus, $newStatus);

        $status = $user->email_verified_at ? 'activated' : 'deactivated';
        return redirect()->route('admin.user_management')->with('success', "User {$status} successfully.");
    }
}
