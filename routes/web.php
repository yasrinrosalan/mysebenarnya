<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

// Controllers
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\Admin\AuditLogController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\InquiryController;
use App\Models\PublicUser;

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

// 📩 Email Verification (Handled by PasswordController)
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', [PasswordController::class, 'verifyEmail'])
    ->middleware(['auth', 'signed'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [PasswordController::class, 'resendVerificationEmail'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');

// 🔐 Forgot & Reset Password
Route::get('/forgot-password', [PasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'reset'])->name('password.update');

// 🔐 Admin Forgot Password
Route::get('/admin/forgot-password', [PasswordController::class, 'showAdminReset'])->name('admin.password.request');
Route::post('/admin/forgot-password', [PasswordController::class, 'sendAdminResetLink'])->name('admin.password.email');

// ✅ Force Password Change
Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', [UserController::class, 'showForcePasswordForm'])->name('password.force.change.form');
    Route::post('/force-password-change', [PasswordController::class, 'updatePassword'])->name('password.force.change');
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

    Route::get('/inquiries', [InquiryController::class, 'agencyIndex'])->name('agency.inquiries.index');
    Route::get('/inquiries/{id}', [InquiryController::class, 'agencyShow'])->name('agency.inquiries.show');


    // Add more agency routes here
});

Route::middleware(['auth', 'isAgency'])->group(function () {
    Route::get('/agency/inquiries/{id}', [InquiryController::class, 'agencyShow'])->name('agency.inquiries.show');
});



// 🛡️ Admin Dashboard & Management
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    Route::get('/register-agency', [AgencyController::class, 'create'])->name('register.agency.form');
    Route::post('/register-agency', [AgencyController::class, 'store'])->name('register.agency');
    Route::post('/inquiries/{id}/handle', [InquiryController::class, 'handle'])->name('inquiries.handle');


    // User management
    Route::get('/users', [AdminController::class, 'index'])->name('users.index');
    Route::get('/users/{id}', [AdminController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [AdminController::class, 'edit'])->name('users.edit');
    Route::put('/users/{id}', [AdminController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [AdminController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-password', [AdminController::class, 'resetPassword'])->name('users.reset-password');

    // Audit logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/{id}', [AuditLogController::class, 'show'])->name('audit-logs.show');

    // Reports
    Route::get('/reports/export-excel', [AdminController::class, 'exportExcel'])->name('reports.excel');
    Route::get('/reports/export-pdf', [AdminController::class, 'exportPDF'])->name('reports.pdf');

    Route::get('/inquiries', [InquiryController::class, 'adminIndex'])->name('inquiries.index');
    Route::get('/inquiry-reports', [InquiryController::class, 'report'])->name('inquiries.report');
    Route::get('/inquiries/manage', [InquiryController::class, 'manage'])->name('inquiries.manage');
    Route::get('/inquiries/{id}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::post('/inquiries/{id}/validate', [InquiryController::class, 'validateInquiry'])->name('inquiries.validate');
    Route::post('/inquiries/{id}/assign', [InquiryController::class, 'assignInquiry'])->name('inquiries.assign');

});
