<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\CustomerManagementController;

Route::middleware(['auth:sanctum'])->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/customer/register', [RegisteredUserController::class, 'store'])->middleware(['auth:sanctum','role:employee'])->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])->name('login');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])->name('password.store');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth:sanctum', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth:sanctum', 'throttle:6,1'])
    ->name('verification.send');

Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::get('/customer/{user_id}', [CustomerManagementController::class, 'view_customer'])->middleware(['auth:sanctum','role:employee'])->name('view_customer');
Route::post('/customer/{user_id}/account', [CustomerManagementController::class, 'add_account'])->middleware(['auth:sanctum','role:employee'])->name('add_account');
Route::post('/transfer', [CustomerManagementController::class, 'transfer_money'])->middleware(['auth:sanctum','role:employee'])->name('transfer_money');
Route::get('/account/{account_number}', [CustomerManagementController::class, 'view_account'])->middleware(['auth:sanctum','role:employee'])->name('view_account');
Route::get('/account/{account_number}/transactions', [CustomerManagementController::class, 'view_account_transactions'])->middleware(['auth:sanctum','role:employee'])->name('view_account_transactions');
