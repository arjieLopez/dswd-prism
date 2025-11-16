# DSWD-PRISM Fresh Migration Setup

## Overview

This directory contains a clean set of migrations representing the current database structure after the November 14, 2025 refactoring. All 56 previous migration files have been backed up and replaced with 18 consolidated migrations.

## Backup Location

Old migrations (56 files) are backed up in:

```
database/migrations_backup_20251114_194633/
```

## New Migration Structure

### Order of Execution:

1. **Lookup Tables** (No foreign keys)

    - `2025_11_14_000001_create_roles_table.php`
    - `2025_11_14_000002_create_designations_table.php`
    - `2025_11_14_000003_create_offices_table.php`
    - `2025_11_14_000004_create_statuses_table.php`
    - `2025_11_14_000005_create_units_table.php`
    - `2025_11_14_000006_create_procurement_modes_table.php`
    - `2025_11_14_000007_create_system_selections_table.php`

2. **User Tables** (Depends on roles, designations, offices)

    - `2025_11_14_000008_create_users_table.php` (includes password_reset_tokens, failed_jobs, personal_access_tokens)

3. **Supplier Tables** (Depends on statuses)

    - `2025_11_14_000009_create_suppliers_table.php`

4. **Purchase Request Tables** (Depends on users, offices, statuses, procurement_modes)

    - `2025_11_14_000010_create_purchase_requests_table.php`
    - `2025_11_14_000011_create_purchase_request_items_table.php`

5. **Purchase Order Tables** (Depends on purchase_requests, suppliers, users, statuses)

    - `2025_11_14_000012_create_purchase_orders_table.php`

6. **Approval & Signature Tables** (Depends on purchase_requests, users)

    - `2025_11_14_000013_create_approvals_table.php`
    - `2025_11_14_000014_create_signatures_table.php`

7. **Document Tables** (Depends on users)

    - `2025_11_14_000015_create_po_documents_table.php`
    - `2025_11_14_000016_create_uploaded_documents_table.php`

8. **Activity & Utility Tables**
    - `2025_11_14_000017_create_user_activities_table.php`
    - `2025_11_14_000018_create_common_attributes_table.php`

## Database Seeders

### Available Seeders:

-   `RoleSeeder` - Seeds 3 roles (admin, staff, user)
-   `StatusSeeder` - Seeds 10 statuses for PRs, suppliers, and POs
-   `DesignationSeeder` - Seeds 11 designations
-   `OfficeSeeder` - Seeds 10 offices
-   `UnitSeeder` - Seeds 20 units of measurement
-   `ProcurementModeSeeder` - Seeds 4 procurement modes
-   `AdminUserSeeder` - Creates default admin user

### Default Admin Credentials:

-   **Email**: admin@dswd.gov.ph
-   **Password**: admin123
-   **Role**: Admin
-   **Designation**: Director
-   **Office**: Office of the Regional Director

## Usage

### Fresh Database Setup:

```bash
# Drop all tables and re-run all migrations with seeders
php artisan migrate:fresh --seed
```

### Migration Only (No Seed):

```bash
# Drop all tables and re-run migrations without seeders
php artisan migrate:fresh
```

### Rollback:

```bash
# Rollback the last migration batch
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset
```

### Re-run Seeders Only:

```bash
# Run all seeders
php artisan db:seed

# Run specific seeder
php artisan db:seed --class=RoleSeeder
```

## Key Changes from Old Structure

### 1. **Consolidated Migrations**

-   Reduced from 56 files to 18 files
-   Represents final database state after refactoring
-   Cleaner migration history for fresh installations

### 2. **Correct Foreign Keys**

-   `purchase_requests.office_id` → `offices.id` (replaced `office_section` varchar)
-   All relationships properly defined with cascading deletes

### 3. **Normalized Structure**

-   All lookup tables (roles, designations, offices, statuses, units, procurement_modes)
-   Foreign key relationships throughout
-   Polymorphic relationships (signatures, common_attributes, personal_access_tokens)

### 4. **Proper Indexes**

-   Unique constraints on lookup table names
-   Composite unique indexes where needed (statuses: context+name)
-   Foreign key indexes for better query performance
-   Polymorphic indexes for morph relationships

## Database Schema Overview

**Total Tables**: 22

**Lookup Tables**: roles, designations, offices, statuses, units, procurement_modes, system_selections

**Core Tables**: users, suppliers, purchase_requests, purchase_request_items, purchase_orders

**Supporting Tables**: approvals, signatures, po_documents, uploaded_documents, user_activities, common_attributes

**Laravel Tables**: password_reset_tokens, failed_jobs, personal_access_tokens, migrations

## Validation

After running migrations, verify the setup:

```bash
# Check migration status
php artisan migrate:status

# Verify tables were created
php artisan tinker
>>> DB::select('SHOW TABLES');

# Verify seeded data
>>> \App\Models\Role::count();        // Should return 3
>>> \App\Models\Status::count();      // Should return 10
>>> \App\Models\Designation::count(); // Should return 11
>>> \App\Models\Office::count();      // Should return 10
>>> \App\Models\Unit::count();        // Should return 20
>>> \App\Models\User::count();        // Should return 1
```

## Notes

-   **Foreign Keys**: All foreign keys have proper constraints with cascading deletes where appropriate
-   **Timestamps**: All tables include `created_at` and `updated_at` columns
-   **Nullable Fields**: Optional fields are marked as `nullable()`
-   **String Lengths**: Optimized VARCHAR lengths based on actual usage
-   **Unique Constraints**: Applied to prevent duplicate entries in lookup tables

## Troubleshooting

### If migration fails:

1. Check database connection in `.env`
2. Ensure MySQL service is running
3. Verify database user has proper permissions
4. Check for existing tables: `php artisan migrate:status`

### If seeding fails:

1. Ensure migrations ran successfully first
2. Check for duplicate unique key violations
3. Verify foreign key relationships exist

### To restore old migrations:

1. Delete new migration files in `database/migrations/`
2. Copy files back from `database/migrations_backup_20251114_194633/`
3. Run `php artisan migrate:fresh --seed`

---

**Last Updated**: November 14, 2025
**Migration Set Version**: 1.0
**Total Migrations**: 18
**Database Schema Version**: Current (post-refactoring)
