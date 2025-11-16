# DSWD-PRISM Test Suite

Comprehensive test coverage for the DSWD-PRISM procurement system, including unit tests, feature tests, and model factories.

## 📋 Test Coverage

### Unit Tests (Model Testing)

-   ✅ **PurchaseRequestTest** - 8 tests covering PR model behavior
-   ✅ **PurchaseOrderTest** - 7 tests covering PO model behavior
-   ✅ **SupplierTest** - 5 tests covering Supplier model behavior

### Feature Tests (Workflow Testing)

-   ✅ **PurchaseRequestWorkflowTest** - 8 tests covering complete PR lifecycle
-   ✅ **PurchaseOrderWorkflowTest** - 7 tests covering PO generation and completion
-   ✅ **NotificationTest** - 4 tests covering notification system
-   ✅ **SupplierManagementTest** - 7 tests covering supplier CRUD operations

**Total: 46 comprehensive tests**

## 🚀 Quick Start

### Run All Tests

```bash
php artisan test
```

### Run Test Suite Interactively

```bash
run-tests.bat
```

This launches an interactive menu for running different test configurations.

### Run Specific Test Suite

```bash
# Unit tests only
php artisan test --testsuite=Unit

# Feature tests only
php artisan test --testsuite=Feature
```

## 📁 Test Structure

```
tests/
├── Unit/                                    # Unit Tests (20 tests)
│   ├── PurchaseRequestTest.php             # 8 tests
│   ├── PurchaseOrderTest.php               # 7 tests
│   └── SupplierTest.php                    # 5 tests
│
├── Feature/                                 # Feature Tests (26 tests)
│   ├── PurchaseRequestWorkflowTest.php     # 8 tests
│   ├── PurchaseOrderWorkflowTest.php       # 7 tests
│   ├── NotificationTest.php                # 4 tests
│   └── SupplierManagementTest.php          # 7 tests
│
├── TEST_DOCUMENTATION.md                    # Comprehensive documentation
├── QUICK_REFERENCE.md                       # Quick command reference
└── README.md                                # This file

database/factories/                          # Test Data Factories
├── PurchaseRequestFactory.php              # PR with states (draft, pending, approved, rejected)
├── PurchaseOrderFactory.php                # PO with states (generated, completed)
├── SupplierFactory.php                     # Supplier with states (active, inactive)
├── PurchaseRequestItemFactory.php          # PR line items
├── UserActivityFactory.php                 # Activities and notifications
├── OfficeFactory.php                       # Office/department records
├── StatusFactory.php                       # Status records
└── UnitFactory.php                         # Units of measurement
```

## 🧪 Test Categories

### 1. Model Tests (Unit)

Test individual model behavior:

-   Relationships (belongsTo, hasMany, etc.)
-   Attribute casting (dates, decimals)
-   Business logic methods
-   Validation rules
-   Data integrity

### 2. Workflow Tests (Feature)

Test complete user workflows:

-   PR creation and submission
-   PR approval/rejection by staff
-   PO generation from approved PR
-   PR completion after delivery
-   Notification system
-   Supplier management
-   Authorization and permissions

## 🔧 Setup

### 1. Configure Test Environment

Create `.env.testing` if it doesn't exist:

```bash
copy .env .env.testing
```

### 2. Configure Test Database

Edit `.env.testing`:

```env
DB_CONNECTION=mysql
DB_DATABASE=dswd_prism_test
# Or use same database with separate prefix
# DB_DATABASE=dswd_prism
```

### 3. Run Migrations (if needed)

```bash
php artisan migrate --env=testing
```

### 4. Generate Autoload Files

```bash
composer dump-autoload
```

## 📊 Running Tests

### Basic Commands

```bash
# All tests
php artisan test

# With verbose output
php artisan test -v

# Stop on first failure
php artisan test --stop-on-failure

# Parallel execution (faster)
php artisan test --parallel
```

### Specific Tests

```bash
# Run specific file
php artisan test tests/Unit/PurchaseRequestTest.php

# Run specific method
php artisan test --filter test_user_can_create_draft_purchase_request
```

### Coverage Reports

```bash
# Generate coverage report
php artisan test --coverage

# With minimum threshold
php artisan test --coverage --min=80
```

## 🏭 Using Factories

Factories generate realistic test data:

```php
// Create a draft PR
$pr = PurchaseRequest::factory()->draft()->create();

// Create PR with items
$pr = PurchaseRequest::factory()->create();
PurchaseRequestItem::factory()->count(5)->create([
    'purchase_request_id' => $pr->id
]);

// Create approved PR
$pr = PurchaseRequest::factory()->approved()->create();

// Create completed PO
$po = PurchaseOrder::factory()->completed()->create();

// Create active supplier
$supplier = Supplier::factory()->active()->create();

// Create staff user
$staff = User::factory()->create(['role' => 'staff']);
```

## ✅ Test Scenarios Covered

### Purchase Request Workflow

-   ✅ User creates draft PR
-   ✅ User submits PR for review
-   ✅ Staff receives notification
-   ✅ Staff approves/rejects PR
-   ✅ User receives approval notification
-   ✅ PR status updates correctly
-   ✅ Users can only view their own PRs
-   ✅ Staff can view all PRs

### Purchase Order Workflow

-   ✅ Staff generates PO from approved PR
-   ✅ PO number auto-generation
-   ✅ Supplier assignment
-   ✅ Staff can edit PO details
-   ✅ User completes PR after delivery
-   ✅ PO marked as completed
-   ✅ PDF export functionality

### Notification System

-   ✅ Notifications created for all staff on PR submission
-   ✅ Users can view their notifications
-   ✅ Notifications can be marked as read
-   ✅ Unread count is accurate

### Supplier Management

-   ✅ Staff can create suppliers
-   ✅ Staff can update supplier details
-   ✅ Staff can activate/deactivate suppliers
-   ✅ Email uniqueness validation
-   ✅ Proper authorization checks
-   ✅ Search functionality

## 🐛 Troubleshooting

### Common Issues

**Tests fail with database errors**

```bash
# Check database configuration
cat .env.testing

# Run migrations
php artisan migrate --env=testing
```

**Class not found errors**

```bash
composer dump-autoload
```

**Slow test execution**

```bash
# Use parallel execution
php artisan test --parallel
```

**Random test failures**

-   Ensure tests use `RefreshDatabase` trait
-   Tests should be independent
-   Don't rely on specific execution order

## 📖 Documentation

-   **[TEST_DOCUMENTATION.md](TEST_DOCUMENTATION.md)** - Comprehensive guide with examples
-   **[QUICK_REFERENCE.md](QUICK_REFERENCE.md)** - Quick command reference
-   **[run-tests.bat](run-tests.bat)** - Interactive test runner

## 🎯 Best Practices

1. **Use Descriptive Test Names**

    ```php
    public function test_staff_can_approve_purchase_request()
    ```

2. **Follow AAA Pattern**

    - Arrange: Set up test data
    - Act: Perform action
    - Assert: Verify results

3. **Use Factories**

    - Don't manually create test data
    - Use factories for consistency

4. **Test One Thing**

    - Each test should verify one behavior
    - Makes debugging easier

5. **Keep Tests Fast**
    - Use `RefreshDatabase` instead of DatabaseTransactions
    - Minimize database queries

## 🔄 CI/CD Integration

### GitHub Actions

```yaml
name: Tests

on: [push, pull_request]

jobs:
    test:
        runs-on: ubuntu-latest
        steps:
            - uses: actions/checkout@v2
            - name: Install Dependencies
              run: composer install
            - name: Run Tests
              run: php artisan test --parallel
```

### GitLab CI

```yaml
test:
    stage: test
    script:
        - composer install
        - php artisan test --coverage
```

## 📈 Coverage Goals

-   **Models**: 90%+ coverage
-   **Controllers**: 85%+ coverage
-   **Critical Workflows**: 100% coverage
-   **Overall**: 85%+ coverage

## 🤝 Contributing

When adding new features:

1. ✅ Write tests first (TDD approach)
2. ✅ Ensure all tests pass
3. ✅ Add factory for new models
4. ✅ Update documentation
5. ✅ Run syntax check: `php -l filename.php`

## 📞 Support

For questions:

-   Review existing test files for patterns
-   Check Laravel Testing documentation
-   Consult project coding standards

---

**Created**: November 16, 2025  
**Framework**: Laravel 10.x with PHPUnit  
**Total Tests**: 46 comprehensive tests
