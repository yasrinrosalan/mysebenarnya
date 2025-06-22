<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\InquiryController;

// 🌐 Public Landing Page
Route::get('/', function () {
    return view('auth.login');
});

// 🔐 Auth Routes using custom AuthController
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/register', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

// 📩 Email Verification (Handled by AuthController now)
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [AuthController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [AuthController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// 🔐 Forgot & Reset Password (Now using AuthController)
Route::get('/forgot-password', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'reset'])->name('password.update');

// 🔐 Admin Forgot Password
Route::get('/admin/forgot-password', [AuthController::class, 'showAdminReset'])->name('admin.password.request');
Route::post('/admin/forgot-password', [AuthController::class, 'sendAdminResetLink'])->name('admin.password.email');

// ✅ Force Password Change
Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', [UserController::class, 'showForcePasswordForm'])->name('password.force.change.form');
    Route::post('/force-password-change', [AuthController::class, 'updatePassword'])->name('password.force.change');
});

// 🧑‍💼 Profile & Registration Info
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', fn() => view('home'))->name('home');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
});

// 🧑‍🎓 Public User Dashboard
Route::middleware(['auth', 'isVerified', 'isPublic'])->group(function () {
    Route::get('/dashboard', fn() => view('public.dashboard'))->name('public.dashboard');

    // 🔍 Inquiry Routes (Module 2)
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('public.inquiries.index');
    Route::get('/inquiries/create', [InquiryController::class, 'create'])->name('public.inquiries.create');
    Route::post('/inquiries', [InquiryController::class, 'store'])->name('public.inquiries.store');
    Route::get('/inquiries/public', [InquiryController::class, 'viewPublic'])->name('public.inquiries.public');
});

// 🏢 Agency User Dashboard
Route::middleware(['auth', 'verified', 'isAgency', 'force.password.change'])->group(function () {
    Route::get('/agency/dashboard', fn() => view('agency.dashboard'))->name('agency.dashboard');
    // Add more agency routes here
});

// 🛡️ Admin Dashboard & Management
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');

    // Register Agency
    Route::get('/register-agency', [UserController::class, 'createAgency'])->name('register.agency.form');
    Route::post('/register-agency', [UserController::class, 'storeAgency'])->name('register.agency');

    // User management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserController::class, 'editUser'])->name('users.edit');
    Route::put('/users/{id}', [UserController::class, 'updateUser'])->name('users.update');
    Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // Reports
    Route::get('/reports/export-excel', [UserController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export-pdf', [UserController::class, 'exportPDF'])->name('reports.pdf');

    // Inquiry Management
    Route::get('/inquiries', [InquiryController::class, 'adminIndex'])->name('inquiries.index');
    Route::get('/inquiry-reports', [InquiryController::class, 'report'])->name('inquiries.report');
    Route::get('/inquiries/manage', [InquiryController::class, 'manage'])->name('inquiries.manage');
    Route::get('/inquiries/{id}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::post('/inquiries/{id}/validate', [InquiryController::class, 'validateInquiry'])->name('inquiries.validate');
    Route::post('/inquiries/{id}/assign', [InquiryController::class, 'assignInquiry'])->name('inquiries.assign');

    
});

Route::middleware(['auth'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/inquiries', [InquiryController::class, 'agencyIndex'])->name('inquiries.index');
    Route::get('/inquiries/{id}', [InquiryController::class, 'agencyShow'])->name('inquiries.show');
    Route::post('/assignments/{id}/update-status', [InquiryController::class, 'updateAssignmentStatus'])->name('assignment.update');

});


