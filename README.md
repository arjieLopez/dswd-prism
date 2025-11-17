# DSWD-PRISM

**Procurement Request Information System and Management**

A comprehensive procurement management system for the Department of Social Welfare and Development (DSWD), built with Laravel 10.

## Overview

DSWD-PRISM streamlines the procurement process by managing Purchase Requests (PRs) and Purchase Orders (POs) through a structured workflow system with role-based access control. The system supports three user roles: Admin, Staff/GSO, and User/Requestor.

## Key Features

-   **Purchase Request Management**: Create, submit, review, and track PRs through their lifecycle
-   **Purchase Order Generation**: Convert approved PRs into POs with supplier assignment
-   **Workflow System**: Status-driven workflow from draft → pending → approved/rejected → PO generated → completed
-   **Role-Based Access Control**: Distinct interfaces and permissions for Admin, Staff, and Users
-   **Supplier Management**: Maintain supplier database with status tracking
-   **Real-time Notifications**: Activity logging and notifications for workflow events
-   **Dashboard Analytics**: Statistics and insights with date filtering (monthly, custom range)
-   **Export Functionality**: Generate Excel (XLSX) and PDF reports
-   **Two-Factor Authentication**: Enhanced security with 2FA
-   **Audit Trail**: Complete activity logging via UserActivity system

## Technology Stack

-   **Framework**: Laravel 10.x
-   **PHP**: 8.1+
-   **Database**: MySQL
-   **Frontend**: Blade Templates, Alpine.js, Tailwind CSS
-   **Build Tool**: Vite
-   **PDF Generation**: barryvdh/laravel-dompdf
-   **Excel Export**: phpoffice/phpspreadsheet
-   **Charts**: Chart.js
-   **Authentication**: Laravel Sanctum

## Prerequisites

-   PHP 8.1 or higher
-   Composer
-   Node.js & npm
-   MySQL 5.7+
-   Laragon (recommended) or similar local development environment

## Installation

1. **Clone the repository**

    ```bash
    git clone https://github.com/arjieLopez/dswd-prism.git
    cd dswd-prism
    ```

2. **Install PHP dependencies**

    ```bash
    composer install
    ```

3. **Install Node dependencies**

    ```bash
    npm install
    ```

4. **Environment setup**

    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

5. **Configure database** (edit `.env`)

    ```env
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=dswd_prism
    DB_USERNAME=root
    DB_PASSWORD=
    ```

6. **Run migrations and seeders**

    ```bash
    php artisan migrate
    php artisan db:seed
    ```

7. **Build assets**

    ```bash
    npm run dev
    # or for production
    npm run build
    ```

8. **Start development server**

    ```bash
    php artisan serve
    ```

9. **Access the application**
    - URL: `http://localhost:8000`
    - Default Admin: Check your seeder for credentials

## Project Structure

```
app/
├── Constants/           # Application constants (Status, Role, Validation)
├── Http/Controllers/    # Application controllers
├── Models/             # Eloquent models
├── Services/           # Business logic (ActivityService)
└── Notifications/      # Email notifications (2FA)

resources/
├── views/
│   ├── admin/          # Admin interface views
│   ├── staff/          # Staff/GSO interface views
│   ├── user/           # User/Requestor interface views
│   └── layouts/        # Layout templates
└── js/
    └── app.js          # Global JS (alerts, exports, modals)

routes/
└── web.php             # Application routes
```

## Core Workflow

### Purchase Request Status Flow

```
draft → pending → approved/rejected → po_generated → completed
```

### User Actions

-   **Requestor**: Create draft PRs, submit for review, withdraw pending PRs, mark as completed
-   **Staff/GSO**: Review pending PRs, approve/reject, generate POs, manage suppliers
-   **Admin**: Full system access, user management, system configurations

### Notification Flow

1. User submits PR → All staff members receive notifications
2. Staff reviews PR → User receives approval/rejection notification
3. PO generated → User receives confirmation notification

## UI/UX Standards

-   **Alert System**: Custom styled alerts (`showSuccessAlert()`, `showErrorAlert()`)
-   **Confirmation Modals**: Gradient-styled modals with animations
-   **Status Colors**: Consistent color coding across the system
    -   Pending: Yellow (`bg-yellow-100 text-yellow-800`)
    -   Approved: Green (`bg-green-100 text-green-800`)
    -   Rejected: Red (`bg-red-100 text-red-800`)
    -   Draft: Gray (`bg-gray-100 text-gray-800`)
-   **Date Format**: `F j, Y` for display, `Y-m-d` for forms
-   **Responsive Design**: Mobile-first approach with Tailwind CSS

## Security Features

-   Two-factor authentication with email verification
-   Role-based middleware protection
-   CSRF protection on all forms and AJAX requests
-   Secure password hashing
-   Activity audit logging

## Key Controllers

-   `PurchaseRequestController`: PR CRUD, submission, completion
-   `PRReviewController`: Staff approval/rejection, export
-   `POGenerationController`: PO creation, editing, printing
-   `SupplierController`: Supplier management
-   `GSODashboardController`: Analytics and statistics
-   `ActivityService`: Centralized activity logging and notifications

## Testing

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Feature
```

## Deployment

1. **Optimize for production**

    ```bash
    composer install --optimize-autoloader --no-dev
    php artisan config:cache
    php artisan route:cache
    php artisan view:cache
    npm run build
    ```

2. **Set environment to production**

    ```env
    APP_ENV=production
    APP_DEBUG=false
    ```

3. **Set proper permissions**
    ```bash
    chmod -R 755 storage bootstrap/cache
    ```

## Development Guidelines

-   Follow PSR-12 coding standards
-   Use repository pattern for complex queries
-   Maintain consistent UI/UX patterns
-   Always use the custom alert system (never basic `alert()`)
-   Log all significant actions via `ActivityService`
-   Include CSRF tokens in all AJAX requests
-   Validate syntax before committing (`php -l filename.php`)
-   Clear cache after configuration changes

## Common Commands

```bash
# Development
php artisan serve                    # Start dev server
npm run dev                         # Watch assets

# Database
php artisan migrate                 # Run migrations
php artisan migrate:fresh --seed    # Fresh migration with seeds
php artisan db:seed                 # Run seeders only

# Cache Management
php artisan config:clear            # Clear config cache
php artisan route:clear             # Clear route cache
php artisan view:clear              # Clear compiled views
php artisan cache:clear             # Clear application cache

# Code Quality
php -l resources/views/file.blade.php  # Check PHP syntax
php artisan view:cache              # Compile blade templates
```

## Support

For questions or issues, please contact the development team or create an issue in the repository.

## License

This project is proprietary software developed for the Department of Social Welfare and Development.

---

**Built with Laravel 10** | **Developed for DSWD** | **© 2024-2025**
