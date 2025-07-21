<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TwoFactorCodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminChartController;

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

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/admin', [AdminChartController::class, 'show'])->middleware(['auth', 'verified', 'twofactor'])->name('admin');
Route::get('/admin/reports', function () {
    return view('admin.reports');
})->middleware(['auth', 'verified'])->name('admin.reports');
Route::get('/admin/user-management', function () {
    return view('admin.user_management');
})->middleware(['auth', 'verified'])->name('admin.user_management');
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

Route::get('/user', function () {
    return view('user.requestingUnit_dashboard');
})->middleware(['auth', 'verified', 'twofactor'])->name('user');
Route::get('/user/requests', function () {
    return view('user.requests');
})->middleware(['auth', 'verified'])->name('user.requests');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get("verify/show", [TwoFactorCodeController::class, 'show'])->name('verify.show');
Route::get("verify/resend", [TwoFactorCodeController::class, 'resend'])->name('verify.resend');
Route::post("verify", [TwoFactorCodeController::class, 'verify'])->name('verify');

require __DIR__ . '/auth.php';
