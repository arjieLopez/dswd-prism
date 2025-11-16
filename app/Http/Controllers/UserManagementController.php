<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\ActivityService;
use App\Services\ExportService;
use App\Services\QueryService;
use App\Constants\PaginationConstants;
use App\Constants\ActivityConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rules;
use Barryvdh\DomPDF\Facade\Pdf;

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
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
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

        $users = $query->with(['role', 'designation', 'office'])->paginate(PaginationConstants::DEFAULT_PER_PAGE)->withQueryString();

        $user = auth()->user();
        $recentActivities = $user->activities()
            ->orderBy('created_at', 'desc')
            ->limit(ActivityConstants::RECENT_ACTIVITY_LIMIT)
            ->get();

        // Get reference data for dropdowns
        $roles = \App\Models\Role::orderBy('name')->get();
        $designations = \App\Models\Designation::orderBy('name')->get();
        $offices = \App\Models\Office::orderBy('name')->get();

        return view('admin.user_management', compact('users', 'recentActivities', 'roles', 'designations', 'offices'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role' => ['required', 'string', 'exists:roles,name'],
            'designation' => ['nullable', 'string', 'exists:designations,name'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'exists:offices,name'],
        ]);

        // Find IDs from names
        $roleId = \App\Models\Role::where('name', $request->role)->first()->id ?? null;
        $designationId = $request->designation ? \App\Models\Designation::where('name', $request->designation)->first()->id : null;
        $officeId = $request->office ? \App\Models\Office::where('name', $request->office)->first()->id : null;

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => $roleId,
            'designation_id' => $designationId,
            'employee_id' => $request->employee_id,
            'office_id' => $officeId,
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
            'role' => ['required', 'string', 'exists:roles,name'],
            'designation' => ['nullable', 'string', 'exists:designations,name'],
            'employee_id' => ['nullable', 'string', 'max:255'],
            'office' => ['nullable', 'string', 'exists:offices,name'],
        ]);

        // Find IDs from names
        $roleId = \App\Models\Role::where('name', $request->role)->first()->id ?? null;
        $designationId = $request->designation ? \App\Models\Designation::where('name', $request->designation)->first()->id : null;
        $officeId = $request->office ? \App\Models\Office::where('name', $request->office)->first()->id : null;

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
            'role_id' => $roleId,
            'designation_id' => $designationId,
            'employee_id' => $request->employee_id,
            'office_id' => $officeId,
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
        $willBeDeactivated = $user->email_verified_at !== null; // User will be deactivated if currently active

        $user->update([
            'email_verified_at' => $user->email_verified_at ? null : now(),
        ]);

        $user->refresh();

        $newStatus = $user->email_verified_at ? 'active' : 'inactive';

        // If user was deactivated, log the action
        if ($willBeDeactivated) {
            // Note: With file-based sessions, the CheckUserStatus middleware
            // will automatically log out inactive users on their next request

            // Log the forced logout action
            ActivityService::logUserLogout($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name);
        }

        ActivityService::logUserStatusChanged($user->id, $user->first_name . ' ' . ($user->middle_name ? $user->middle_name . ' ' : '') . $user->last_name, $oldStatus, $newStatus);

        $status = $user->email_verified_at ? 'activated' : 'deactivated';

        if ($user->email_verified_at) {
            $message = "User {$status} successfully.";
        } else {
            $message = "User {$status} successfully. They will be automatically logged out on their next request.";
        }

        return redirect()->route('admin.user_management')->with('success', $message);
    }

    public function exportXlsx(Request $request)
    {
        $query = User::query();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'name') {
            $query->orderByRaw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) $sortOrder");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->with(['role', 'designation', 'office'])->get();

        // Prepare headers
        $headers = ['#', 'Name', 'Email Address', 'Role', 'Status', 'Designation', 'Employee ID', 'Office', 'Date Created'];

        // Prepare rows
        $rows = [];
        foreach ($users as $index => $user) {
            $fullName = $user->first_name . ($user->middle_name ? ' ' . $user->middle_name : '') . ' ' . $user->last_name;
            $status = $user->email_verified_at ? 'Active' : 'Inactive';

            $rows[] = [
                $index + 1,
                $fullName,
                $user->email,
                ucfirst($user->role),
                $status,
                $user->designation ?? '',
                $user->employee_id ?? '',
                $user->office ?? '',
                $user->created_at->format('M j, Y')
            ];
        }

        // Use ExportService
        $exportService = new ExportService();
        $filename = $exportService->generateFilename('users', 'csv');

        return $exportService->exportToCSV($headers, $rows, $filename);
    }
    public function exportPdf(Request $request)
    {
        $query = User::query();

        // Apply same filters as index method
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where(DB::raw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name)"), 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role') && $request->role !== 'all') {
            $query->whereHas('role', function ($q) use ($request) {
                $q->where('name', $request->role);
            });
        }

        if ($request->filled('status') && $request->status !== 'all') {
            if ($request->status === 'active') {
                $query->whereNotNull('email_verified_at');
            } else {
                $query->whereNull('email_verified_at');
            }
        }

        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');

        if ($sortBy === 'name') {
            $query->orderByRaw("CONCAT(first_name, ' ', IFNULL(middle_name, ''), ' ', last_name) $sortOrder");
        } else {
            $query->orderBy($sortBy, $sortOrder);
        }

        $users = $query->with(['role', 'designation', 'office'])->get();

        // Use ExportService for filter summary and PDF generation
        $exportService = new ExportService();

        $filters = [
            'search' => $request->search,
            'role' => $request->role,
            'status' => $request->status,
            'sort_by' => $request->sort_by,
            'sort_order' => $request->get('sort_order', 'desc')
        ];

        $filterSummary = $exportService->prepareFilterSummary($filters);

        $data = [
            'users' => $users,
            'filterSummary' => $filterSummary,
            'totalUsers' => $users->count(),
            'exportDate' => now()->format('F j, Y g:i A')
        ];

        $filename = $exportService->generateFilename('users', 'pdf');

        return $exportService->exportToPDF('exports.users_pdf', $data, $filename, 'landscape');
    }
}
