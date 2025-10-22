# DSWD-PRISM AI Coding Instructions

## Project Overview

DSWD-PRISM is a Laravel 10 procurement system for the Department of Social Welfare and Development. It manages Purchase Requests (PRs) and Purchase Orders (POs) through a workflow-based system with role-based access control (admin, staff/GSO, user/requestor).

## Core Workflow

PR Status Flow: `draft` → `pending` (submit) → `approved/rejected` (staff review) → `po_generated` (PO creation) → `completed` (delivery).

## Domain Model

-   **PurchaseRequest**: Core entity with items, supplier, status.
-   **PurchaseOrder**: Generated from approved PRs.
-   **User**: Roles with activities/notifications.
-   **Supplier**: Vendors with status management.
-   **UserActivity**: Audit logging and notifications.

## Critical Controllers & Responsibilities

-   `PurchaseRequestController`: User CRUD, submit/withdraw/complete, staff notifications.
-   `PRReviewController`: Staff approval/rejection, export.
-   `POGenerationController`: PO creation, supplier assignment, edit/print.
-   `SupplierController`: Supplier CRUD with alert feedback.
-   `GSODashboardController`: Statistics with date filtering.
-   `ActivityService`: Centralized logging and notifications.

## Development Patterns

### Standardized Alert System

Use `showSuccessAlert(message)` and `showErrorAlert(message)` for all user feedback. Never use `alert()`. Integrate with session messages and AJAX responses.

Example:

```javascript
showSuccessAlert(data.message);
setTimeout(() => location.reload(), 1500);
```

### Notification System

On PR submission, notify all staff via `UserActivity`:

```php
$staffUsers = User::where('role', 'staff')->get();
foreach ($staffUsers as $staff) {
    UserActivity::create([...]);
}
```

### Date Filtering & Formatting

Dashboards use three modes: this_month, previous_month, custom. Format display as `F j, Y`, forms as `Y-m-d`.

### Pagination

Use `appends(request()->query())` for filter preservation. Show only if >10 items.

### Status Colors

Models use `getStatusColorAttribute()` with match expressions:

```php
return match ($this->status) {
    'pending' => 'bg-yellow-100 text-yellow-800',
    // ...
};
```

### AJAX Pattern

Include CSRF token, handle success/error with alerts:

```javascript
fetch(url, {
    method: "POST",
    headers: { "X-CSRF-TOKEN": token, "Content-Type": "application/json" },
    body: JSON.stringify(data),
})
    .then((response) => response.json())
    .then((data) => {
        if (data.success) showSuccessAlert(data.message);
        else showErrorAlert(data.message);
    });
```

### Export Functionality

Global JS in `app.js` for XLSX/PDF export with filter preservation.

### Modal Pattern

Use `<x-modal>` with Alpine.js. Functions: `openViewModal(id)`, `closeEditModal()`.

### Number to Words

For PO amounts, use custom function for pesos/centavos.

## Key Files

-   Controllers: `app/Http/Controllers/`
-   Models: `app/Models/`
-   Views: `resources/views/` (Blade + Alpine.js)
-   Routes: `routes/web.php`
-   Services: `app/Services/ActivityService.php`

## Dependencies

-   `barryvdh/laravel-dompdf`: PDF generation
-   `phpoffice/phpspreadsheet`: Excel export
-   `laravel/sanctum`: API auth
-   Chart.js, Tailwind CSS, Vite

## Development Workflow

-   Serve: `php artisan serve`
-   Assets: `npm run dev` / `npm run build`
-   DB: `php artisan migrate` / `db:seed`
-   Cache: `php artisan config:clear` etc.
-   Test: PHPUnit in `tests/`

## Code Quality & Validation

### Syntax Checking

Always validate code syntax after making changes to ensure error-free implementation:

```bash
# Check PHP syntax in Blade templates
php -l resources/views/admin/system_selections.blade.php

# Clear view cache after template changes
php artisan view:clear

# Check for compilation errors
php artisan view:cache
```

**Validation Workflow:**

1. **Syntax Check**: Use `php -l` to validate PHP syntax in all modified files
2. **View Cache**: Clear view cache with `php artisan view:clear` after Blade template changes
3. **Error Verification**: Ensure no syntax, runtime, or logic errors before task completion
4. **Testing**: Run relevant tests to verify functionality works as expected

## Security

-   Two-factor auth with middleware
-   Role-based routes with middleware
-   CSRF on all forms/AJAX

## Common Pitfalls

-   Separate PO Generated/Completed cards
-   Consistent field mapping (e.g., `total`, not `total_amount`)
-   Use `startOfDay()` for overdue checks
-   Always log activities via `ActivityService`

### Standard Confirmation Dialogue

Use modern styled confirmation modals instead of browser's basic `confirm()`. Always use the `showConfirmationModal()` function with the exact styling pattern:

```javascript
function showConfirmationModal(message, onConfirm) {
    const confirmDiv = document.createElement("div");
    confirmDiv.style.cssText = `
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        backdrop-filter: blur(4px);
        z-index: 99999;
        display: flex;
        align-items: center;
        justify-content: center;
        animation: fadeIn 0.2s ease-out;
    `;

    const modalDiv = document.createElement("div");
    modalDiv.style.cssText = `
        background: white;
        padding: 32px 28px;
        border-radius: 16px;
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
        max-width: 440px;
        width: 90%;
        text-align: center;
        animation: slideIn 0.3s ease-out;
        transform-origin: center center;
    `;

    modalDiv.innerHTML = `
        <div style="margin-bottom: 20px;">
            <div style="width: 64px; height: 64px; margin: 0 auto 16px; background: linear-gradient(135deg, #FEF3C7, #F59E0B); border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                <svg style="width: 32px; height: 32px; color: #D97706;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <h3 style="margin: 0 0 8px 0; font-size: 20px; font-weight: 600; color: #1F2937;">Confirm Action</h3>
            <p style="margin: 0; color: #6B7280; font-size: 15px; line-height: 1.5;">${message}</p>
        </div>
        <div style="display: flex; gap: 12px; justify-content: center;">
            <button id="confirm-yes" style="
                padding: 12px 24px;
                background: linear-gradient(135deg, #EF4444, #DC2626);
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.2s ease;
                box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
            " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(239, 68, 68, 0.4)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(239, 68, 68, 0.3)'">
                Yes, Confirm
            </button>
            <button id="confirm-no" style="
                padding: 12px 24px;
                background: linear-gradient(135deg, #6B7280, #4B5563);
                color: white;
                border: none;
                border-radius: 8px;
                cursor: pointer;
                font-weight: 600;
                font-size: 14px;
                transition: all 0.2s ease;
                box-shadow: 0 4px 12px rgba(107, 114, 128, 0.3);
            " onmouseover="this.style.transform='translateY(-1px)'; this.style.boxShadow='0 6px 16px rgba(107, 114, 128, 0.4)'"
               onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 12px rgba(107, 114, 128, 0.3)'">
                Cancel
            </button>
        </div>
    `;

    confirmDiv.appendChild(modalDiv);
    document.body.appendChild(confirmDiv);

    // Add CSS animations
    const style = document.createElement("style");
    style.textContent = `
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: scale(0.8) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }
        @keyframes fadeOut {
            from { opacity: 1; }
            to { opacity: 0; }
        }
    `;
    document.head.appendChild(style);

    // Event listeners
    document.getElementById("confirm-yes").onclick = () => {
        confirmDiv.remove();
        onConfirm();
    };

    document.getElementById("confirm-no").onclick = () => {
        confirmDiv.style.animation = "fadeOut 0.2s ease-in";
        setTimeout(() => confirmDiv.remove(), 200);
    };

    confirmDiv.onclick = (e) => {
        if (e.target === confirmDiv) {
            confirmDiv.style.animation = "fadeOut 0.2s ease-in";
            setTimeout(() => confirmDiv.remove(), 200);
        }
    };

    // Cleanup style when modal closes
    setTimeout(() => {
        if (style.parentNode) {
            style.remove();
        }
    }, 5000);
}
```

**Key Features:**

-   Warning icon with yellow/orange gradient background
-   Gradient buttons with hover effects (lift + enhanced shadows)
-   Smooth animations (fadeIn, slideIn, fadeOut)
-   Backdrop blur effect
-   Click outside to close
-   Professional typography and spacing

### Form Input Standards

**Placeholder Text Color**: All input field placeholders must use gray text (`text-gray-500` or `placeholder-gray-500`) to clearly distinguish them from user input:

```html
<!-- ✅ Correct: Gray placeholder text -->
<input
    type="text"
    placeholder="Enter your name..."
    class="placeholder-gray-500 border border-gray-300 rounded-lg px-3 py-2"
/>

<!-- ❌ Incorrect: Default black placeholder -->
<input
    type="text"
    placeholder="Enter your name..."
    class="border border-gray-300 rounded-lg px-3 py-2"
/>
```

## 🚨 CRITICAL: System-Wide Style Uniformity

**This is the highest priority requirement.** All UI elements, spacing, colors, typography, and interactions must be perfectly consistent across the entire application. Study existing pages and copy their exact patterns.

### Uniformity Requirements:

-   **Spacing**: Use consistent Tailwind classes (`mt-6`, `px-4 py-2`, `space-x-4`, etc.)
-   **Colors**: Match status colors exactly (`bg-yellow-100 text-yellow-800` for pending)
-   **Typography**: Same font weights, sizes, and hierarchy across all pages
-   **Components**: Reuse existing modal, button, and card patterns
-   **Interactions**: Same hover effects, transitions, and animations
-   **Layout**: Consistent grid systems, card layouts, and responsive behavior

### Before implementing any UI:

1. **Reference existing pages** - Copy styling from similar functionality
2. **Check multiple pages** - Ensure consistency across user, staff, and admin sections
3. **Use exact classes** - Don't approximate; copy the precise Tailwind combinations
4. **Test responsiveness** - Ensure mobile/tablet behavior matches other pages

**Example**: All dashboard cards use `bg-white rounded-xl shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300` - use this exact pattern everywhere.

## 🚨 CRITICAL GUIDELINES FOR AI AGENTS

### **Database & Implementation Safety**

-   **NO DATABASE CHANGES**: Never create migrations, seeders, or modify database structure
-   **NO CONTROLLER ROUTES**: Do not add new routes without explicit user permission
-   **CHECK EXISTING CODE**: Always search for existing implementations before adding methods
-   **CONSISTENCY FIRST**: Reference similar files to maintain design patterns and avoid redundancy
-   **AVOID DUPLICATION**: Before implementing any new feature, method, or component, thoroughly search the codebase to ensure it doesn't already exist

-   **ERROR-FREE CODE**: All generated code must be free of syntax, runtime, and logic errors. Always validate and fix any errors before considering the task complete.

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
