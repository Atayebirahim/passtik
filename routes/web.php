<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RouterController;
use App\Http\Controllers\VoucherController;

Route::get('/', function () {
    return view('welcome');
});

Route::resource('routers', RouterController::class);
Route::get('/routers/{router}/check', [RouterController::class, 'checkStatus'])->name('routers.check');
Route::get('/routers/{router}/manage', [RouterController::class, 'manage'])->name('routers.manage');
Route::post('/routers/{router}/generate', [VoucherController::class, 'generate'])->name('routers.generate');
Route::delete('/routers/{router}/vouchers/clear', [RouterController::class, 'clearVouchers'])->name('routers.vouchers.clear');
Route::get('/routers/{router}/traffic', [RouterController::class, 'networkTraffic'])->name('routers.traffic');

Route::resource('vouchers', VoucherController::class);
Route::get('/vouchers/print/sheet', [VoucherController::class, 'print'])->name('vouchers.print');
Route::get('/vouchers-reports', [VoucherController::class, 'reports'])->name('vouchers.reports');
Route::get('/redeem', [VoucherController::class, 'redeemPage'])->name('vouchers.redeem.page');
Route::post('/api/vouchers/redeem', [VoucherController::class, 'redeem'])->name('vouchers.redeem')->withoutMiddleware(\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class);
Route::post('/vouchers/bulk/delete', [VoucherController::class, 'bulkDelete'])->name('vouchers.bulk.delete');
Route::get('/vouchers/bulk/export', [VoucherController::class, 'bulkExport'])->name('vouchers.bulk.export');
Route::post('/vouchers/bulk/expire', [VoucherController::class, 'bulkExpire'])->name('vouchers.bulk.expire');
