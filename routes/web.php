<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\UserController;
use App\Http\Controllers\Admin\UserManagementController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;
use App\Http\Controllers\Admin\AgencyController;
use App\Http\Controllers\InquiryController;
use App\Models\PublicUser;

// 🌐 Public Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 🔐 Auth Routes with Email Verification
Auth::routes(['verify' => true]);

// 📩 Email Verification
Route::get('/email/verify', function () {
    return view('auth.verify');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();

    $user = Auth::user();
    if ($user->role === 'public') {
        PublicUser::where('user_id', $user->id)->update([
            'registered_at' => now(),
        ]);
    }

    return match ($user->role) {
        'admin' => redirect('/admin/dashboard'),
        'agency' => redirect('/agency/dashboard'),
        'public' => redirect('/dashboard'),
        default => redirect('/home'),
    };
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('status', 'verification-link-sent');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 🔐 Forgot & Reset Password
Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

// 🔐 Admin Forgot Password
Route::get('/admin/forgot-password', [ForgotPasswordController::class, 'showAdminReset'])->name('admin.password.request');
Route::post('/admin/forgot-password', [ForgotPasswordController::class, 'sendAdminResetLink'])->name('admin.password.email');

// ✅ Force Password Change
Route::middleware(['auth'])->group(function () {
    Route::get('/force-password-change', [UserController::class, 'showForcePasswordForm'])->name('force.password.form');
    Route::post('/force-password-change', [UserController::class, 'updateForcedPassword'])->name('force.password.update');
});

// 🧑‍💼 Profile
Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/home', fn() => view('home'))->name('home');
    Route::get('/profile', [UserController::class, 'showProfile'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile/edit', [ProfileController::class, 'update'])->name('profile.update');
});

// 🧑‍🎓 Public User
Route::middleware(['auth', 'isVerified', 'isPublic'])->group(function () {
    Route::get('/dashboard', fn() => view('public.dashboard'))->name('public.dashboard');

    // 🔍 Inquiry Routes (Module 2)
    Route::get('/inquiries', [InquiryController::class, 'index'])->name('public.inquiries.index');
    Route::get('/inquiries/create', [InquiryController::class, 'create'])->name('public.inquiries.create');
    Route::post('/inquiries', [InquiryController::class, 'store'])->name('public.inquiries.store');
    Route::get('/inquiries/public', [InquiryController::class, 'viewPublic'])->name('public.inquiries.public');
});

// 🏢 Agency User
Route::middleware(['auth', 'verified', 'isAgency'])->prefix('agency')->name('agency.')->group(function () {
    Route::get('/dashboard', fn() => view('agency.dashboard'))->name('dashboard');

    // (Coming next) Inquiry assignment tracking, status updates, history
    // Route::get('/inquiries', [...]);
});

// 🛡️ Admin Routes
Route::middleware(['auth', 'isAdmin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', fn() => view('admin.dashboard'))->name('dashboard');

    Route::get('/register-agency', [AgencyController::class, 'create'])->name('register.agency.form');
    Route::post('/register-agency', [AgencyController::class, 'store'])->name('register.agency');

    Route::get('/users', [UserManagementController::class, 'index'])->name('users.index');
    Route::get('/users/filter', [UserManagementController::class, 'filter'])->name('users.filter');
    Route::get('/users/{id}', [UserManagementController::class, 'show'])->name('users.show');
    Route::get('/users/{id}/edit', [UserManagementController::class, 'edit'])->name('users.edit');
    Route::post('/users/{id}/update', [UserManagementController::class, 'update'])->name('users.update');
    Route::delete('/users/{id}', [UserManagementController::class, 'destroy'])->name('users.destroy');
    Route::post('/users/{id}/reset-password', [UserManagementController::class, 'resetPassword'])->name('users.reset_password');

    Route::get('/inquiries', [InquiryController::class, 'adminIndex'])->name('inquiries.index');
    Route::get('/inquiry-reports', [InquiryController::class, 'report'])->name('inquiries.report');
    Route::get('/inquiries/manage', [InquiryController::class, 'manage'])->name('inquiries.manage');
    Route::get('/inquiries/{id}', [InquiryController::class, 'show'])->name('inquiries.show');
    Route::post('/inquiries/{id}/validate', [InquiryController::class, 'validateInquiry'])->name('inquiries.validate');
    Route::post('/inquiries/{id}/assign', [InquiryController::class, 'assignInquiry'])->name('inquiries.assign');

    // (Coming next) Inquiry review, filtering, reports
    // Route::get('/inquiries', [...]);
});
