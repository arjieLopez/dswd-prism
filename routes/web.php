<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorCodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\PurchaseRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/welcome', function () {
    return view('welcome');
});

Route::get('/', function () {
    return view('auth.login');
    // return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', [AdminDashboardController::class, 'show'])->middleware(['auth', 'verified', 'twofactor'])->name('admin');
Route::get('/admin/reports', [App\Http\Controllers\ReportsController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.reports');
Route::post('/admin/reports/export', [App\Http\Controllers\ReportsController::class, 'export'])->middleware(['auth', 'verified'])->name('admin.reports.export');
// User Management Routes
Route::get('/admin/user-management', [App\Http\Controllers\UserManagementController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.user_management');
Route::post('/admin/user-management', [App\Http\Controllers\UserManagementController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.user_management.store');
Route::put('/admin/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.user_management.update');
Route::delete('/admin/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.user_management.destroy');
Route::patch('/admin/user-management/{user}/toggle-status', [App\Http\Controllers\UserManagementController::class, 'toggleStatus'])->middleware(['auth', 'verified'])->name('admin.user_management.toggle-status');
// Audit Logs Routes
Route::get('/admin/audit-logs', [App\Http\Controllers\AuditLogsController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.audit_logs');
Route::post('/admin/audit-logs/export', [App\Http\Controllers\AuditLogsController::class, 'export'])->middleware(['auth', 'verified'])->name('admin.audit_logs.export');

Route::get('/staff', [App\Http\Controllers\GSODashboardController::class, 'show'])->middleware(['auth', 'verified', 'twofactor'])->name('staff');
Route::get('/staff/po-generation', function () {
    return view('staff.po_generation');
})->middleware(['auth', 'verified'])->name('staff.po_generation');
Route::get('/staff/suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->middleware(['auth', 'verified'])->name('staff.suppliers');
// PR Review Routes
Route::get('/staff/pr-review', [App\Http\Controllers\PRReviewController::class, 'index'])->middleware(['auth', 'verified'])->name('staff.pr_review');
Route::get('/staff/pr-review/{purchaseRequest}/data', [App\Http\Controllers\PRReviewController::class, 'show'])->middleware(['auth', 'verified'])->name('staff.pr_review.data');
Route::post('/staff/pr-review/{purchaseRequest}/approve', [App\Http\Controllers\PRReviewController::class, 'approve'])->middleware(['auth', 'verified'])->name('staff.pr_review.approve');
Route::post('/staff/pr-review/{purchaseRequest}/reject', [App\Http\Controllers\PRReviewController::class, 'reject'])->middleware(['auth', 'verified'])->name('staff.pr_review.reject');
// PO Generation Routes
Route::get('/staff/po-generation', [App\Http\Controllers\POGenerationController::class, 'index'])->middleware(['auth', 'verified'])->name('staff.po_generation');
Route::get('/staff/po-generation/{purchaseRequest}/data', [App\Http\Controllers\POGenerationController::class, 'show'])->middleware(['auth', 'verified'])->name('staff.po_generation.data');
Route::post('/staff/po-generation/{purchaseRequest}/generate-po', [App\Http\Controllers\POGenerationController::class, 'generatePO'])->middleware(['auth', 'verified'])->name('staff.po_generation.generate');
Route::get('/staff/po-generation/{purchaseRequest}/view', [App\Http\Controllers\POGenerationController::class, 'viewPO'])->middleware(['auth', 'verified'])->name('staff.po_generation.view');
Route::get('/staff/po-generation/{purchaseRequest}/edit', [App\Http\Controllers\POGenerationController::class, 'editPO'])->middleware(['auth', 'verified'])->name('staff.po_generation.edit');
Route::post('/staff/po-generation/{purchaseRequest}/edit', [App\Http\Controllers\POGenerationController::class, 'updatePO'])->middleware(['auth', 'verified'])->name('staff.po_generation.update');
Route::get('/staff/po-generation/{purchaseRequest}/print', [\App\Http\Controllers\POGenerationController::class, 'printPO'])->name('po.print');
// PO Document Routes
Route::get('/po-documents/upload', [App\Http\Controllers\PODocumentController::class, 'upload'])->middleware(['auth', 'verified'])->name('po-documents.upload');
Route::post('/po-documents', [App\Http\Controllers\PODocumentController::class, 'store'])->middleware(['auth', 'verified'])->name('po-documents.store');
Route::get('/po-documents/{poDocument}/download', [App\Http\Controllers\PODocumentController::class, 'download'])->middleware(['auth', 'verified'])->name('po-documents.download');
Route::delete('/po-documents/{poDocument}', [App\Http\Controllers\PODocumentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('po-documents.destroy');
// PO Generation Form Routes
Route::get('/staff/generate-po/{purchaseRequest}', [App\Http\Controllers\POGenerationController::class, 'showGenerateForm'])->middleware(['auth', 'verified'])->name('staff.generate_po.form');
Route::post('/staff/generate-po/{purchaseRequest}', [App\Http\Controllers\POGenerationController::class, 'storeGeneratedPO'])->middleware(['auth', 'verified'])->name('staff.generate_po.store');
// Supplier Management Routes
Route::get('/suppliers', [App\Http\Controllers\SupplierController::class, 'index'])->middleware(['auth', 'verified'])->name('suppliers.index');
Route::post('/suppliers', [App\Http\Controllers\SupplierController::class, 'store'])->middleware(['auth', 'verified'])->name('suppliers.store');
Route::get('/suppliers/{supplier}', [App\Http\Controllers\SupplierController::class, 'show'])->middleware(['auth', 'verified'])->name('suppliers.show');
Route::post('/suppliers/{supplier}', [App\Http\Controllers\SupplierController::class, 'update'])->middleware(['auth', 'verified'])->name('suppliers.update');
Route::post('/suppliers/{supplier}/toggle-status', [App\Http\Controllers\SupplierController::class, 'toggleStatus'])->middleware(['auth', 'verified'])->name('suppliers.toggle-status');
Route::delete('/suppliers/{supplier}', [App\Http\Controllers\SupplierController::class, 'destroy'])->middleware(['auth', 'verified'])->name('suppliers.destroy');


Route::get('/user', [UserDashboardController::class, 'show'])->middleware(['auth', 'verified', 'twofactor'])->name('user');
Route::get('/user/requests', [PurchaseRequestController::class, 'index'])->middleware(['auth', 'verified'])->name('user.requests');

Route::get('/purchase-requests/{purchaseRequest}/data', [PurchaseRequestController::class, 'getData'])->middleware(['auth', 'verified'])->name('purchase-requests.data');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
    Route::post('/purchase-requests', [PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
    Route::post('/purchase-requests/{purchaseRequest}/update', [PurchaseRequestController::class, 'update'])->name('purchase-requests.update');
    Route::delete('/purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->name('purchase-requests.destroy');
    Route::post('/purchase-requests/{purchaseRequest}/submit', [PurchaseRequestController::class, 'submit'])->name('purchase-requests.submit');
    Route::post('/purchase-requests/{purchaseRequest}/withdraw', [PurchaseRequestController::class, 'withdraw'])->name('purchase-requests.withdraw');
    Route::get('/purchase-requests/{purchaseRequest}/print', [PurchaseRequestController::class, 'print'])->name('purchase-requests.print');
});
// Uploaded Documents Routes
Route::get('/uploaded-documents/upload', [App\Http\Controllers\UploadedDocumentController::class, 'upload'])->middleware(['auth', 'verified'])->name('uploaded-documents.upload');
Route::post('/uploaded-documents', [App\Http\Controllers\UploadedDocumentController::class, 'store'])->middleware(['auth', 'verified'])->name('uploaded-documents.store');
Route::get('/uploaded-documents/{uploadedDocument}/download', [App\Http\Controllers\UploadedDocumentController::class, 'download'])->middleware(['auth', 'verified'])->name('uploaded-documents.download');
Route::delete('/uploaded-documents/{uploadedDocument}', [App\Http\Controllers\UploadedDocumentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('uploaded-documents.destroy');

// Export Uploaded Documents
Route::post('/uploaded-documents/export/xlsx', [App\Http\Controllers\UploadedDocumentController::class, 'exportXLSX'])->middleware(['auth', 'verified'])->name('uploaded-documents.export.xlsx');
Route::post('/uploaded-documents/export/pdf', [App\Http\Controllers\UploadedDocumentController::class, 'exportPDF'])->middleware(['auth', 'verified'])->name('uploaded-documents.export.pdf');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("verify/show", [TwoFactorCodeController::class, 'show'])->name('verify.show');
Route::get("verify/resend", [TwoFactorCodeController::class, 'resend'])->name('verify.resend');
Route::post("verify", [TwoFactorCodeController::class, 'verify'])->name('verify');

require __DIR__ . '/auth.php';
