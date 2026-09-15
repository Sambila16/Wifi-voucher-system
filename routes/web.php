<?php

use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\PlanController;
use App\Http\Controllers\Admin\RouterController;
use App\Http\Controllers\Admin\VoucherController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\HotspotController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public landing page
|--------------------------------------------------------------------------
*/
Route::get('/', fn () => view('welcome'))->name('home');

/*
|--------------------------------------------------------------------------
| Login (no email verification set up, so just auth)
|--------------------------------------------------------------------------
*/
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Public hotspot routes (reachable from the walled garden — no auth)
|--------------------------------------------------------------------------
*/
Route::get('/hotspot', [HotspotController::class, 'landing'])->name('hotspot.landing');
Route::post('/hotspot/login', [HotspotController::class, 'loginWithVoucher'])->name('hotspot.login');
Route::post('/hotspot/pay', [HotspotController::class, 'initiatePayment'])->name('hotspot.pay');
Route::get('/hotspot/payments/{payment}/status', [HotspotController::class, 'paymentStatus'])->name('hotspot.payment.status');

// Gateway webhook — must be excluded from CSRF protection in bootstrap/app.php
Route::post('/webhooks/payment', [HotspotController::class, 'paymentWebhook'])->name('webhooks.payment');

/*
|--------------------------------------------------------------------------
| Admin panel (behind auth + admin middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/vouchers', [VoucherController::class, 'index'])->name('vouchers.index');
    Route::post('/vouchers/manual', [VoucherController::class, 'storeManual'])->name('vouchers.manual');
    Route::post('/vouchers/{voucher}/revoke', [VoucherController::class, 'revoke'])->name('vouchers.revoke');
    Route::post('/vouchers/{voucher}/retry', [VoucherController::class, 'retry'])->name('vouchers.retry');

    Route::get('/plans', [PlanController::class, 'index'])->name('plans.index');
    Route::post('/plans', [PlanController::class, 'store'])->name('plans.store');
    Route::put('/plans/{plan}', [PlanController::class, 'update'])->name('plans.update');
    Route::delete('/plans/{plan}', [PlanController::class, 'destroy'])->name('plans.destroy');
    Route::post('/plans/{plan}/enable', [PlanController::class, 'enable'])->name('plans.enable');
    Route::delete('/plans/{plan}/force', [PlanController::class, 'forceDelete'])->name('plans.force-delete');

    Route::get('/routers', [RouterController::class, 'index'])->name('routers.index');
    Route::post('/routers', [RouterController::class, 'store'])->name('routers.store');
    Route::post('/routers/{router}/test', [RouterController::class, 'testConnection'])->name('routers.test');
    Route::get('/routers/{router}/sessions', [RouterController::class, 'activeSessions'])->name('routers.sessions');
    Route::post('/routers/{router}/kick/{voucherCode}', [RouterController::class, 'kick'])->name('routers.kick');
});
