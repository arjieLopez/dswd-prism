# DSWD-PRISM AI Coding Instructions

## Project Overview

DSWD-PRISM is a Laravel 10 procurement system for the Department of Social Welfare and Development. It manages Purchase Requests (PRs) and Purchase Orders (POs) through a workflow-based system with role-based access control.

## Architecture & Core Workflow

### Domain Model

-   **Purchase Requests (PRs)**: Core entity with statuses: `draft` → `pending` → `approved/rejected` → `po_generated` → `completed`
-   **Purchase Request Items**: Line items within a PR (quantity, unit_cost, item_description)
-   **Purchase Orders (POs)**: Generated from approved PRs with supplier and procurement details
-   **Suppliers**: External vendors with status management (`active`/`inactive`)
-   **Users**: Three roles - `admin`, `staff` (GSO), and `user` (requestor)
-   **PODocuments**: Uploaded PO documents with file management
-   **UploadedDocuments**: Scanned PR documents attached to purchase requests

### Key Business Logic

```php
// PR Status Flow
'draft' → 'pending' (submit) → 'approved/rejected' (staff review) → 'po_generated' (PO creation) → 'completed' (delivery)

// Status Colors (in PurchaseRequest model using match expressions)
'pending' => 'bg-yellow-100 text-yellow-800'
'approved' => 'bg-green-100 text-green-800'
'po_generated' => 'bg-blue-100 text-blue-800'
'completed' => 'bg-indigo-100 text-indigo-800'
```

### Critical Controllers & Their Responsibilities

-   `PurchaseRequestController`: User CRUD operations, submit/withdraw/complete actions
-   `PRReviewController`: Staff approval/rejection workflow with export functionality
-   `POGenerationController`: Convert approved PRs to POs, manage supplier assignment, edit/print POs
-   `PODocumentController`: Handle PO document uploads/downloads/deletion
-   `UploadedDocumentController`: Manage scanned document uploads with export features
-   `SupplierController`: Supplier CRUD operations with status management
-   `UserManagementController`: Admin user management with role/status controls
-   `GSODashboardController`: Statistics and overview for GSO staff with date filtering
-   `UserDashboardController`: User-specific dashboard with month/custom date filtering
-   `ActivityService`: Centralized activity logging across the system

## Development Patterns

### Dashboard Filtering Pattern

All dashboards implement consistent date filtering with three modes:

```php
// Controller pattern for date filtering
$filterType = $request->get('filter_type', 'this_month');
$dateFrom = $request->get('date_from');
$dateTo = $request->get('date_to');

// Set date range based on filter type
if ($filterType === 'this_month') {
    $startDate = Carbon::now()->startOfMonth();
    $endDate = Carbon::now()->endOfMonth();
} elseif ($filterType === 'previous_month') {
    $startDate = Carbon::now()->subMonth()->startOfMonth();
    $endDate = Carbon::now()->subMonth()->endOfMonth();
} elseif ($filterType === 'custom' && $dateFrom && $dateTo) {
    $startDate = Carbon::createFromFormat('Y-m-d', $dateFrom)->startOfDay();
    $endDate = Carbon::createFromFormat('Y-m-d', $dateTo)->endOfDay();
}
```

### Pagination with Filter Preservation

Use `appends(request()->query())` to maintain filter parameters across pagination:

```blade
{{ $items->appends(request()->query())->links() }}
// Custom pagination with preserved filters
<a href="{{ $items->appends(request()->query())->url($page) }}">
```

### Status Color System

Models use `getStatusColorAttribute()` and `getStatusDisplayAttribute()` with match expressions:

```php
public function getStatusColorAttribute()
{
    return match ($this->status) {
        'draft' => 'bg-gray-100 text-gray-800',
        'pending' => 'bg-yellow-100 text-yellow-800',
        'approved' => 'bg-green-100 text-green-800',
        'po_generated' => 'bg-blue-100 text-blue-800',
        'completed' => 'bg-indigo-100 text-indigo-800',
        default => 'bg-gray-100 text-gray-800',
    };
}
```

### Overdue Logic Pattern

For date comparisons, use `startOfDay()` to avoid time-based issues:

```php
// Only mark items overdue if delivery date is strictly before today
$isOverdue = $item->date_of_delivery && $item->date_of_delivery->startOfDay()->lt(now()->startOfDay());
```

### Blade Component Architecture

```php
// Standard page layout pattern
<x-page-layout>
    <x-slot name="header">
        <x-app-header :homeUrl="route('staff')" :userName="..." :recentActivities="..." />
    </x-slot>
    <!-- Content -->
</x-page-layout>
```

### Modal Pattern

Use `<x-modal name="unique-name">` with Alpine.js for dynamic forms. JavaScript functions follow naming: `openViewModal(id)`, `closeEditModal()`.

```javascript
// Standard modal opening pattern with fetch API
function openViewModal(prId) {
    fetch(`/staff/po-generation/${prId}/data`, {
        method: "GET",
        headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            // Populate modal fields
            document.getElementById("view-pr-number").textContent =
                data.pr_number;
            // Show modal using Alpine.js event system
            window.dispatchEvent(
                new CustomEvent("open-modal", {
                    detail: "view-pr-modal",
                })
            );
        });
}
```

### Export Functionality Pattern

All list views implement consistent export dropdown with XLSX/PDF options:

```javascript
// Export dropdown toggle pattern
document.getElementById("export-btn").addEventListener("click", function (e) {
    e.stopPropagation();
    document.getElementById("export-dropdown").classList.toggle("hidden");
});

// Export with current filters preserved
document.getElementById("export-xlsx").addEventListener("click", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const formData = new FormData();

    // Preserve all active filters
    if (urlParams.get("search"))
        formData.append("search", urlParams.get("search"));
    if (urlParams.get("status"))
        formData.append("status", urlParams.get("status"));
    if (urlParams.get("date_from"))
        formData.append("date_from", urlParams.get("date_from"));

    // Add CSRF token
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (csrfToken) formData.append("_token", csrfToken);

    fetch("/staff/pr-review/export/xlsx", { method: "POST", body: formData })
        .then((response) => response.blob())
        .then((blob) => {
            // Download file
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download =
                "export_" +
                new Date().toISOString().slice(0, 19).replace(/:/g, "-") +
                ".xlsx";
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });
});
```

### AJAX Pattern with CSRF

All AJAX requests must include CSRF token:

```javascript
// Standard AJAX pattern for form submissions
fetch(`/staff/pr-review/${prId}/approve`, {
    method: "POST",
    headers: {
        "X-CSRF-TOKEN": document
            .querySelector('meta[name="csrf-token"]')
            .getAttribute("content"),
        "Content-Type": "application/json",
    },
    body: JSON.stringify(requestData),
})
    .then((response) => response.json())
    .then((data) => {
        if (data.success) {
            alert("Success message");
            location.reload(); // or redirect
        } else {
            alert("Error: " + data.message);
        }
    });
```

### Supplier Auto-fill Pattern

Forms with supplier selection auto-populate related fields:

```javascript
// Auto-fill supplier address and TIN from data attributes
function updateSupplierInfo() {
    const supplierSelect = document.getElementById('supplier_id');
    const selectedOption = supplierSelect.options[supplierSelect.selectedIndex];

    document.getElementById('supplier_address').value = selectedOption.getAttribute('data-address') || '';
    document.getElementById('supplier_tin').value = selectedOption.getAttribute('data-tin') || '';
}

// Blade template pattern for supplier options
@foreach($suppliers as $supplier)
    <option value="{{ $supplier->id }}"
            data-address="{{ $supplier->address }}"
            data-tin="{{ $supplier->tin }}">
        {{ $supplier->supplier_name }}
    </option>
@endforeach
```

### Activity Logging

Always use `ActivityService` for audit trails:

```php
ActivityService::logPrApproved($prNumber, $staffName);
ActivityService::logPoGenerated($prNumber, $poNumber);
```

### User Name Display Pattern

Consistent full name formatting throughout views:

```php
Auth::user()->first_name . (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') . ' ' . Auth::user()->last_name
```

## Database Relationships

### Key Model Relationships

```php
PurchaseRequest::class
- belongsTo(User::class)
- belongsTo(Supplier::class)
- hasMany(PurchaseRequestItem::class, 'items')

User::class
- hasMany(PurchaseRequest::class)
- hasMany(UserActivity::class, 'activities')
```

### Migration Patterns

-   Use `foreignId()->constrained()->onDelete('cascade')` for relationships
-   Decimal fields use `decimal(15, 2)` for currency, `decimal(12, 2)` for items
-   Status fields are strings with specific enum-like values

## Frontend Patterns

### Styling Conventions

-   Tailwind CSS with consistent color schemes per status
-   Card layouts: `bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300`
-   Icons via Iconify: `<i class="iconify" data-icon="mdi:icon-name"></i>`
-   Grid layouts commonly use `grid-cols-3` or `grid-cols-4` for dashboards

### JavaScript Integration

-   Alpine.js for component interactivity
-   Chart.js for dashboard visualizations with global window variables pattern
-   Fetch API for AJAX operations with CSRF token handling
-   Form validation follows Laravel's validation patterns

## Critical Dependencies

### Key Packages

-   `barryvdh/laravel-dompdf`: PDF generation for PRs/POs
-   `phpoffice/phpspreadsheet`: Excel export functionality
-   `laravel/sanctum`: API authentication
-   `blade-ui-kit/blade-heroicons`: Icon components

### Build Tools

-   Vite for asset compilation (`npm run dev` / `npm run build`)
-   Tailwind CSS with forms plugin
-   PostCSS with autoprefixer

## Development Workflow

### Common Commands

```bash
# Development
php artisan serve
npm run dev

# Database
php artisan migrate
php artisan db:seed

# Cache clearing
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Testing Environment

Uses PHPUnit with Feature/Unit test structure. Laravel Breeze provides authentication scaffolding.

## Security & Authentication

### Two-Factor Authentication

Custom middleware `TwoFactorMiddleware` redirects to verification if 2FA code exists and is valid.

### Role-Based Access

Routes grouped by role with middleware:

-   `admin`: User management, reports, audit logs
-   `staff`: PR review, PO generation, GSO dashboard
-   `user`: Create/manage own PRs

### File Uploads

Stored in `storage/app/public` with specific patterns for PR signatures and PO documents.

## Common Pitfalls & Solutions

### Dashboard Card Separation

Keep PO Generated and Completed cards separate - PO Generated should only count `status = 'po_generated'`, not completed items:

```php
// Correct: PO Generated card counts only po_generated status
$poGenerated = PurchaseRequest::where('status', 'po_generated')
    ->whereBetween('po_generated_at', [$startDate, $endDate])->count();

// Correct: Completed card counts only completed status
$completed = PurchaseRequest::where('status', 'completed')
    ->whereBetween('updated_at', [$startDate, $endDate])->count();
```

### Dashboard Variables

When adding new dashboard cards, ensure controller passes all required variables:

```php
// In GSODashboardController
return view('staff.gso_dashboard', compact(
    'pendingPRs', 'approvedPRs', 'completedPRs', // Add new variable
    'pendingPercentageChange', 'approvedPercentageChange', 'completedPercentageChange' // Add new calculation
));
```

### Status Management

Always validate status transitions in controllers before database updates. Use helper methods in models for status color classes.

### Export Functionality

PDF/Excel exports use jobs pattern for large datasets. Always include CSRF tokens in export forms.

## File Structure Notes

-   Custom layouts in `resources/views/layouts/page.blade.php` (with sidebar)
-   Modal components follow Alpine.js patterns with x-data
-   Controllers follow RESTful naming but include custom actions (approve, reject, generate)
-   Services in `app/Services/` for cross-cutting concerns like activity logging

## Advanced JavaScript Patterns

### Global Variable Pattern for Dynamic Data

Pass server data to JavaScript using Blade directives:

```php
// In Blade template
<script>
    window.suppliers = @json($suppliers);
</script>

// In JavaScript
function buildSupplierOptions() {
    let options = '<option value="">Select Supplier</option>';
    window.suppliers.forEach(s => {
        options += `<option value="${s.id}" data-address="${s.address}">${s.supplier_name}</option>`;
    });
    return options;
}
```

### Chart.js Integration Pattern

Dashboard charts use global window variables:

```javascript
// In app.js
document.addEventListener("DOMContentLoaded", () => {
    const el = document.getElementById("bar-chart");
    if (el && window.chartLabels && window.chartPR && window.chartPO) {
        new Chart(el, {
            type: "bar",
            data: {
                labels: window.chartLabels,
                datasets: [
                    {
                        label: "Total PR",
                        data: window.chartPR,
                        backgroundColor: "rgba(71, 74, 255, 0.9)",
                    },
                    {
                        label: "Total PO",
                        data: window.chartPO,
                        backgroundColor: "rgba(77, 206, 65, 0.9)",
                    },
                ],
            },
        });
    }
});
```

### Alpine.js Date Picker Pattern

Complex filtering with custom date picker integration:

```javascript
function openCustomDatePicker() {
    const filterElement = document.getElementById("filter-dropdown");
    if (
        filterElement &&
        filterElement._x_dataStack &&
        filterElement._x_dataStack[0]
    ) {
        filterElement._x_dataStack[0].open = false;
        setTimeout(() => {
            filterElement._x_dataStack[0].showDatePicker = true;
        }, 100);
    }
}
```
