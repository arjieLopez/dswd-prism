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
-   **UserActivity**: Notification system for tracking all system actions

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

-   `PurchaseRequestController`: User CRUD operations, submit/withdraw/complete actions, **staff notifications**
-   `PRReviewController`: Staff approval/rejection workflow with export functionality
-   `POGenerationController`: Convert approved PRs to POs, manage supplier assignment, edit/print POs
-   `PODocumentController`: Handle PO document uploads/downloads/deletion
-   `UploadedDocumentController`: Manage scanned document uploads with export features
-   `SupplierController`: Supplier CRUD operations with **consistent alert feedback**
-   `UserManagementController`: Admin user management with role/status controls
-   `GSODashboardController`: Statistics and overview for GSO staff with date filtering
-   `UserDashboardController`: User-specific dashboard with month/custom date filtering
-   `ActivityService`: Centralized activity logging and **notification creation**

## Development Patterns

### **CRITICAL: Standardized Alert System**

**ALL pages must use the same JavaScript alert functions** - never use basic `alert()` or inconsistent styling:

```javascript
// Required on every page that shows user feedback
function showSuccessAlert(message) {
    const alertDiv = document.createElement("div");
    alertDiv.style.cssText = `
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        background: #10B981;
        color: white;
        padding: 16px 20px;
        border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        z-index: 99999;
        font-weight: 500;
        font-size: 16px;
        text-align: center;
        min-width: 300px;
        max-width: 400px;
    `;
    alertDiv.textContent = message;

    const closeBtn = document.createElement("button");
    closeBtn.textContent = "×";
    closeBtn.style.cssText = `
        position: absolute;
        top: 5px;
        right: 10px;
        background: none;
        border: none;
        color: white;
        font-size: 18px;
        cursor: pointer;
        line-height: 1;
    `;
    closeBtn.onclick = () => alertDiv.remove();
    alertDiv.appendChild(closeBtn);

    document.body.appendChild(alertDiv);
    setTimeout(() => alertDiv.parentNode && alertDiv.remove(), 3000);
}

function showErrorAlert(message) {
    // Same structure but background: #EF4444 for red
}
```

**Alert Integration Patterns:**

-   **Laravel session messages**: Convert to JavaScript alerts in Blade templates
-   **AJAX responses**: Always call `showSuccessAlert(data.message)` on success
-   **Form submissions**: Show success alert then delayed reload: `setTimeout(() => location.reload(), 1500)`

### Notification System Pattern

When users submit PRs, **automatically notify all staff**:

```php
// In PurchaseRequestController submit method
$staffUsers = User::where('role', 'staff')->get();
foreach ($staffUsers as $staff) {
    UserActivity::create([
        'user_id' => $staff->id,
        'action' => 'pr_submitted_notification',
        'details' => "New PR {$pr->pr_number} submitted by {$user->first_name} {$user->last_name}",
        'related_type' => 'App\Models\PurchaseRequest',
        'related_id' => $pr->id,
    ]);
}
```

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

Use `appends(request()->query())` to maintain filter parameters across pagination. All list views use custom pagination that only shows when more than 10 items exist:

```blade
<!-- Custom Pagination Pattern (used in suppliers, PR review, PO generation, user requests) -->
@if ($items->total() > 10)
    <div class="flex justify-center mt-6">
        <div class="flex items-center space-x-1">
            <!-- Previous/Next buttons with SVG icons -->
            @if ($items->onFirstPage())
                <span class="px-3 py-2 text-gray-400 cursor-not-allowed">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                </span>
            @else
                <a href="{{ $items->appends(request()->query())->previousPageUrl() }}"
                   class="px-3 py-2 text-gray-600 hover:text-blue-600 transition-colors">
                    <!-- SVG icon -->
                </a>
            @endif

            <!-- Page number logic with ellipsis -->
            @php
                $start = max(1, $items->currentPage() - 2);
                $end = min($items->lastPage(), $items->currentPage() + 2);
                // Smart pagination logic to show 5 pages when possible
            @endphp

            <!-- Page links preserve filters -->
            <a href="{{ $items->appends(request()->query())->url($page) }}"
               class="px-3 py-2 text-sm font-medium text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-md">
        </div>
    </div>
@endif
```

### Counter Column Pattern

All list tables include sequential numbering that works across pagination:

```blade
<!-- Table counter that preserves pagination -->
{{ ($items->currentPage() - 1) * $items->perPage() + $index + 1 }}
<!-- For @foreach($items as $index => $item) loops -->

{{ ($items->currentPage() - 1) * $items->perPage() + $loop->iteration }}
<!-- For @foreach without key when using $loop variable -->
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

All list views implement consistent export dropdown with XLSX/PDF options using global JavaScript in `app.js`:

```blade
<!-- Standard export dropdown HTML (copy this pattern) -->
<div class="relative inline-block text-left">
    <button id="export-btn" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
        <i class="iconify w-4 h-4 mr-2" data-icon="material-symbols:upload"></i>
        Export
    </button>
    <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
        <button id="export-xlsx" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="iconify w-4 h-4 mr-2" data-icon="vscode-icons:file-type-excel"></i>
            Export as XLSX
        </button>
        <button id="export-pdf" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="iconify w-4 h-4 mr-2" data-icon="vscode-icons:file-type-pdf2"></i>
            Export as PDF
        </button>
    </div>
</div>
```

```javascript
// Global export functionality pattern (in app.js)
// Automatically detects current page and routes to appropriate export endpoint
document.getElementById("export-xlsx")?.addEventListener("click", function () {
    const urlParams = new URLSearchParams(window.location.search);
    const formData = new FormData();

    // Preserve all active filters
    if (urlParams.get("search"))
        formData.append("search", urlParams.get("search"));
    if (urlParams.get("status"))
        formData.append("status", urlParams.get("status"));
    if (urlParams.get("date_from"))
        formData.append("date_from", urlParams.get("date_from"));

    // Route detection for different pages
    const currentPath = window.location.pathname;
    let exportUrl = "/purchase-requests/export/xlsx";
    if (currentPath.includes("pr-review"))
        exportUrl = "/staff/pr-review/export/xlsx";
    else if (currentPath.includes("po-generation"))
        exportUrl = "/staff/po-generation/export/xlsx";

    // CSRF token and file download logic
    const csrfToken = document
        .querySelector('meta[name="csrf-token"]')
        ?.getAttribute("content");
    if (csrfToken) formData.append("_token", csrfToken);

    fetch(exportUrl, { method: "POST", body: formData })
        .then((response) => response.blob())
        .then((blob) => {
            // Auto-download with timestamp
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement("a");
            a.href = url;
            a.download =
                "export_" +
                new Date().toISOString().slice(0, 19).replace(/:/g, "-") +
                ".xlsx";
            document.body.appendChild(a);
            a.click();
            // Cleanup
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);
        });
});
```

```blade
<!-- Standard export dropdown HTML (copy this pattern) -->
<div class="relative inline-block text-left">
    <button id="export-btn" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-md">
        <i class="iconify w-4 h-4 mr-2" data-icon="material-symbols:upload"></i>
        Export
    </button>
    <div id="export-dropdown" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-10">
        <button id="export-xlsx" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="iconify w-4 h-4 mr-2" data-icon="vscode-icons:file-type-excel"></i>
            Export as XLSX
        </button>
        <button id="export-pdf" class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex items-center">
            <i class="iconify w-4 h-4 mr-2" data-icon="vscode-icons:file-type-pdf2"></i>
            Export as PDF
        </button>
    </div>
</div>
```

### AJAX Pattern with CSRF

All AJAX requests must include CSRF token and use standardized alerts:

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
            showSuccessAlert(data.message);
            setTimeout(() => location.reload(), 1500); // Show alert before reload
        } else {
            showErrorAlert(data.message || "An error occurred");
        }
    })
    .catch((error) => {
        showErrorAlert("Network error occurred");
    });
```

### Professional Confirmation Modal Pattern

Replace basic `confirm()` dialogs with styled confirmation modals:

```javascript
// Professional confirmation for critical actions (supplier status, etc.)
function showConfirmationModal(message, onConfirm) {
    const confirmDiv = document.createElement("div");
    confirmDiv.style.cssText = `
        position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.5); z-index: 99999;
        display: flex; align-items: center; justify-content: center;
    `;

    const modalDiv = document.createElement("div");
    modalDiv.style.cssText = `
        background: white; padding: 24px; border-radius: 8px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.2); max-width: 400px; text-align: center;
    `;

    modalDiv.innerHTML = `
        <h3 style="margin: 0 0 16px 0; font-size: 18px; font-weight: 600;">Confirm Action</h3>
        <p style="margin: 0 0 24px 0; color: #666;">${message}</p>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="confirm-yes" style="padding: 8px 16px; background: #EF4444; color: white; border: none; border-radius: 4px; cursor: pointer;">Yes</button>
            <button id="confirm-no" style="padding: 8px 16px; background: #6B7280; color: white; border: none; border-radius: 4px; cursor: pointer;">Cancel</button>
        </div>
    `;

    confirmDiv.appendChild(modalDiv);
    document.body.appendChild(confirmDiv);

    document.getElementById("confirm-yes").onclick = () => {
        confirmDiv.remove();
        onConfirm();
    };
    document.getElementById("confirm-no").onclick = () => confirmDiv.remove();
    confirmDiv.onclick = (e) => {
        if (e.target === confirmDiv) confirmDiv.remove();
    };
}
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

### Activity Logging & Notifications

Always use `ActivityService` for audit trails and notifications:

```php
ActivityService::logPrApproved($prNumber, $staffName);
ActivityService::logPoGenerated($prNumber, $poNumber);
ActivityService::logPrSubmitted($prNumber, $userName);
```

**UserActivity** model serves dual purpose:

-   Audit logging for administrator reports
-   Notification system for user bell icons (filter by `user_id` for notifications)

```php
// Creating notifications for specific users
UserActivity::create([
    'user_id' => $targetUserId, // Who receives the notification
    'action' => 'pr_submitted_notification',
    'details' => "New PR {$prNumber} submitted",
    'related_type' => 'App\Models\PurchaseRequest',
    'related_id' => $prId,
]);
```

### Number to Words Pattern

For PDF documents requiring amount in words (particularly PO print layouts):

```php
@php
    function numberToWords($number) {
        $ones = array(
            0 => '', 1 => 'ONE', 2 => 'TWO', 3 => 'THREE', 4 => 'FOUR', 5 => 'FIVE',
            6 => 'SIX', 7 => 'SEVEN', 8 => 'EIGHT', 9 => 'NINE', 10 => 'TEN',
            11 => 'ELEVEN', 12 => 'TWELVE', 13 => 'THIRTEEN', 14 => 'FOURTEEN', 15 => 'FIFTEEN',
            16 => 'SIXTEEN', 17 => 'SEVENTEEN', 18 => 'EIGHTEEN', 19 => 'NINETEEN'
        );

        $tens = array(
            0 => '', 2 => 'TWENTY', 3 => 'THIRTY', 4 => 'FORTY', 5 => 'FIFTY',
            6 => 'SIXTY', 7 => 'SEVENTY', 8 => 'EIGHTY', 9 => 'NINETY'
        );

        if ($number < 20) {
            return $ones[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ($number % 10 != 0 ? ' ' . $ones[$number % 10] : '');
        } elseif ($number < 1000) {
            return $ones[intval($number / 100)] . ' HUNDRED' . ($number % 100 != 0 ? ' ' . numberToWords($number % 100) : '');
        } elseif ($number < 1000000) {
            return numberToWords(intval($number / 1000)) . ' THOUSAND' . ($number % 1000 != 0 ? ' ' . numberToWords($number % 1000) : '');
        } elseif ($number < 1000000000) {
            return numberToWords(intval($number / 1000000)) . ' MILLION' . ($number % 1000000 != 0 ? ' ' . numberToWords($number % 1000000) : '');
        }
        return 'NUMBER TOO LARGE';
    }

    $total = $purchaseRequest->total ?? 0;
    $pesos = floor($total);
    $centavos = round(($total - $pesos) * 100);

    $totalInWords = '';
    if ($pesos > 0) {
        $totalInWords = numberToWords($pesos) . ' PESOS';
    }
    if ($centavos > 0) {
        $totalInWords .= ($pesos > 0 ? ' AND ' : '') . numberToWords($centavos) . ' CENTAVOS';
    }
    if ($pesos == 0 && $centavos == 0) {
        $totalInWords = 'ZERO PESOS';
    }
    $totalInWords .= ' ONLY';
@endphp
{{ $totalInWords }}
```

### Date Formatting Pattern

Consistent date formatting across the application:

```php
// For display dates (October 10, 2025 format)
{{ $item->date_field ? \Carbon\Carbon::parse($item->date_field)->format('F j, Y') : '' }}

// For form dates (YYYY-MM-DD format)
{{ $item->date_field ? $item->date_field->format('Y-m-d') : '' }}

// For timestamps with time (audit logs only)
{{ $item->updated_at->format('M j, Y g:i A') }}
```

### User Name Display Pattern

Consistent full name formatting throughout views:

```php
Auth::user()->first_name . (Auth::user()->middle_name ? ' ' . Auth::user()->middle_name : '') . ' ' . Auth::user()->last_name
```

### Date Formatting Pattern

Consistent date formatting across the application:

```php
// For display dates (October 10, 2025 format)
{{ $item->date_field ? \Carbon\Carbon::parse($item->date_field)->format('F j, Y') : '' }}

// For form dates (YYYY-MM-DD format)
{{ $item->date_field ? $item->date_field->format('Y-m-d') : '' }}

// For timestamps with time
{{ $item->updated_at->format('M j, Y g:i A') }}
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

### Field Mapping Consistency

Ensure consistent field mapping across templates and controllers:

```php
// Common field mappings to watch for
$pr->total (not $pr->total_amount)
$pr->po_generated_at (not $pr->po_date)
$pr->delivery_address ?? $pr->place_of_delivery
$pr->items (relationship, not single item)
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

## 🚨 CRITICAL GUIDELINES FOR AI AGENTS

### **Database & Implementation Safety**

-   **NO DATABASE CHANGES**: Never create migrations, seeders, or modify database structure
-   **NO CONTROLLER ROUTES**: Do not add new routes without explicit user permission
-   **CHECK EXISTING CODE**: Always search for existing implementations before adding methods
-   **CONSISTENCY FIRST**: Reference similar files to maintain design patterns and avoid redundancy

### **Code Review Process**

```bash
# Always check if method/pattern exists before implementing
1. Search for similar functionality: grep -r "methodName" app/
2. Check related controllers for existing patterns
3. Review similar blade templates for UI consistency
4. Verify alert system usage matches established patterns
```

## 🎯 AI Agent Quick Reference

**MUST-USE Patterns (Never Deviate):**

1. **Alert System**: Use `showSuccessAlert()` / `showErrorAlert()` - NEVER basic `alert()`
2. **Notifications**: Auto-notify staff on PR submissions via `UserActivity`
3. **Date Format**: `F j, Y` for display, `Y-m-d` for forms, `M j, Y g:i A` for audit logs only
4. **AJAX Flow**: `showSuccessAlert(data.message)` → `setTimeout(() => location.reload(), 1500)`
5. **Confirmations**: Custom styled modals, not `confirm()`
6. **Design Consistency**: Ensure uniform styling, spacing, and behavior across ALL files

**Key Files for Reference:**

-   `resources/views/user/requests.blade.php` - Master alert implementation
-   `app/Services/ActivityService.php` - Notification patterns
-   `app/Http/Controllers/PurchaseRequestController.php` - Staff notification logic
-   `resources/views/staff/suppliers.blade.php` - Confirmation modal example
