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
Route::get('/admin/reports', function () {
    return view('admin.reports');
})->middleware(['auth', 'verified'])->name('admin.reports');

Route::get('/admin/user-management', [App\Http\Controllers\UserManagementController::class, 'index'])->middleware(['auth', 'verified'])->name('admin.user_management');
Route::post('/admin/user-management', [App\Http\Controllers\UserManagementController::class, 'store'])->middleware(['auth', 'verified'])->name('admin.user_management.store');
Route::put('/admin/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'update'])->middleware(['auth', 'verified'])->name('admin.user_management.update');
Route::delete('/admin/user-management/{user}', [App\Http\Controllers\UserManagementController::class, 'destroy'])->middleware(['auth', 'verified'])->name('admin.user_management.destroy');
Route::patch('/admin/user-management/{user}/toggle-status', [App\Http\Controllers\UserManagementController::class, 'toggleStatus'])->middleware(['auth', 'verified'])->name('admin.user_management.toggle-status');

Route::get('/admin/audit-logs', function () {
    return view('admin.audit_logs');
})->middleware(['auth', 'verified'])->name('admin.audit_logs');

Route::get('/staff', function () {
    return view('staff.gso_dashboard');
})->middleware(['auth', 'verified', 'twofactor'])->name('staff');
Route::get('/staff/pr-review', function () {
    return view('staff.pr_review');
})->middleware(['auth', 'verified'])->name('staff.pr_review');
Route::get('/staff/po-generation', function () {
    return view('staff.po_generation');
})->middleware(['auth', 'verified'])->name('staff.po_generation');
Route::get('/staff/suppliers', function () {
    return view('staff.suppliers');
})->middleware(['auth', 'verified'])->name('staff.suppliers');

Route::get('/user', [UserDashboardController::class, 'show'])->middleware(['auth', 'verified', 'twofactor'])->name('user');
Route::get('/user/requests', [PurchaseRequestController::class, 'index'])->middleware(['auth', 'verified'])->name('user.requests');

Route::get('/purchase-requests/{purchaseRequest}/data', [PurchaseRequestController::class, 'getData'])->middleware(['auth', 'verified'])->name('purchase-requests.data');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/purchase-requests/create', [PurchaseRequestController::class, 'create'])->name('purchase-requests.create');
    Route::post('/purchase-requests', [PurchaseRequestController::class, 'store'])->name('purchase-requests.store');
    Route::post('/purchase-requests/{purchaseRequest}/update', [PurchaseRequestController::class, 'update'])->name('purchase-requests.update');
    Route::delete('/purchase-requests/{purchaseRequest}', [PurchaseRequestController::class, 'destroy'])->name('purchase-requests.destroy');
});
// Uploaded Documents Routes
Route::get('/uploaded-documents/upload', [App\Http\Controllers\UploadedDocumentController::class, 'upload'])->middleware(['auth', 'verified'])->name('uploaded-documents.upload');
Route::post('/uploaded-documents', [App\Http\Controllers\UploadedDocumentController::class, 'store'])->middleware(['auth', 'verified'])->name('uploaded-documents.store');
Route::get('/uploaded-documents/{uploadedDocument}/download', [App\Http\Controllers\UploadedDocumentController::class, 'download'])->middleware(['auth', 'verified'])->name('uploaded-documents.download');
Route::delete('/uploaded-documents/{uploadedDocument}', [App\Http\Controllers\UploadedDocumentController::class, 'destroy'])->middleware(['auth', 'verified'])->name('uploaded-documents.destroy');


Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("verify/show", [TwoFactorCodeController::class, 'show'])->name('verify.show');
Route::get("verify/resend", [TwoFactorCodeController::class, 'resend'])->name('verify.resend');
Route::post("verify", [TwoFactorCodeController::class, 'verify'])->name('verify');

require __DIR__ . '/auth.php';
