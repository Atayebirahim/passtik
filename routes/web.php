<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\VoucherController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;

Route::get('/', function () {
    return view('landing');
});

Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->middleware('throttle:5,1');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->middleware('throttle:5,1');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::get('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('/forgot-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'sendResetLinkEmail'])->middleware('throttle:3,1')->name('password.email');
Route::get('/reset-password/{token}', [\App\Http\Controllers\Auth\PasswordResetController::class, 'showResetForm'])->name('password.reset');
Route::post('/reset-password', [\App\Http\Controllers\Auth\PasswordResetController::class, 'reset'])->middleware('throttle:5,1')->name('password.update');

Route::middleware('auth')->group(function () {
    Route::get('/email/verify', function () {
        return view('auth.verify-email');
    })->name('verification.notice');

    Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
        $request->fulfill();
        return redirect()->route('routers.index')->with('alert_success', 'Email verified! Welcome to Passtik.');
    })->middleware('signed')->name('verification.verify');

    Route::post('/email/verification-notification', function (\Illuminate\Http\Request $request) {
        $request->user()->sendEmailVerificationNotification();
        return back()->with('alert_success', 'Verification link sent!');
    })->middleware('throttle:6,1')->name('verification.send');
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/upgrade', [\App\Http\Controllers\SubscriptionController::class, 'showUpgradePage'])->name('subscription.upgrade');
    Route::post('/subscription/request', [\App\Http\Controllers\SubscriptionController::class, 'requestUpgrade'])->name('subscription.request');
    
    Route::resource('routers', RouterController::class);
    Route::get('/routers/{router}/check', [RouterController::class, 'checkStatus'])->name('routers.check');
    Route::get('/routers/{router}/manage', [RouterController::class, 'manage'])->name('routers.manage');
    Route::post('/routers/{router}/generate', [VoucherController::class, 'generate'])->name('routers.generate');
    Route::delete('/routers/{router}/vouchers/clear', [RouterController::class, 'clearVouchers'])->name('routers.vouchers.clear');
    Route::get('/routers/{router}/traffic', [RouterController::class, 'networkTraffic'])->name('routers.traffic');

    Route::resource('vouchers', VoucherController::class);
    Route::get('/vouchers/print/sheet', [VoucherController::class, 'print'])->name('vouchers.print');
    Route::get('/vouchers-reports', [VoucherController::class, 'reports'])->name('vouchers.reports');
    Route::post('/vouchers/bulk/delete', [VoucherController::class, 'bulkDelete'])->name('vouchers.bulk.delete');
    Route::get('/vouchers/bulk/export', [VoucherController::class, 'bulkExport'])->name('vouchers.bulk.export');
    Route::post('/vouchers/bulk/expire', [VoucherController::class, 'bulkExpire'])->name('vouchers.bulk.expire');
});

Route::get('/redeem', [VoucherController::class, 'redeemPage'])->name('vouchers.redeem.page');
Route::post('/api/vouchers/redeem', [VoucherController::class, 'redeem'])->middleware('throttle:10,1')->name('vouchers.redeem')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);

Route::get('/terms', function () { return view('legal.terms'); })->name('terms');
Route::get('/privacy', function () { return view('legal.privacy'); })->name('privacy');

// Admin routes (add proper admin middleware later)
Route::middleware(['auth', 'verified'])->prefix('admin')->group(function () {
    Route::get('/subscriptions', [\App\Http\Controllers\SubscriptionController::class, 'adminIndex'])->name('admin.subscriptions');
    Route::post('/subscriptions/{id}/approve', [\App\Http\Controllers\SubscriptionController::class, 'approve'])->name('admin.subscription.approve');
    Route::post('/subscriptions/{id}/reject', [\App\Http\Controllers\SubscriptionController::class, 'reject'])->name('admin.subscription.reject');
});
