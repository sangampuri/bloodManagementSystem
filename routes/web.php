<?php

use Illuminate\Support\Facades\Route;
use App\Support\BloodGroup;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonorSearchController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;


Route::view('/','home');
// Guests only: logged-in users get bounced to their dashboard instead.
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show']);
    Route::post('/register', [RegisterController::class, 'store']);
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');
});

// Any logged-in user, any role, can log out.
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

Route::get('/dashboard', [DashboardController::class, 'index'])->middleware('auth');
Route::get('/donor', [DonorController::class, 'edit'])->middleware('auth');
Route::post('/donor', [DonorController::class, 'save'])->middleware('auth');

Route::get('/admin', [DashboardController::class, 'index'])->middleware(['auth', 'admin']);




Route::middleware('auth')->group(function () {
    Route::get('/donor', [DonorController::class, 'edit']);
    Route::post('/donor', [DonorController::class, 'save']);
});
Route::get('/profile', [ProfileController::class, 'edit'])->middleware('auth');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth');

Route::get('/donors', [DonorSearchController::class, 'index']);
Route::get('/availability', [AvailabilityController::class, 'index'])->middleware('auth');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit']);
Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update']);
    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/create', [InventoryController::class, 'create']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory/{batch}/edit', [InventoryController::class, 'edit']);
    Route::put('/inventory/{batch}', [InventoryController::class, 'update']);
    Route::delete('/inventory/{batch}', [InventoryController::class, 'destroy']);
    Route::get('/requests', [RequestController::class, 'index']);
Route::post('/requests/{bloodRequest}/status', [RequestController::class, 'updateStatus']);
});

Route::middleware('auth')->group(function () {
    Route::get('/requests/create', [BloodRequestController::class, 'create']);
    Route::post('/requests', [BloodRequestController::class, 'store']);
    Route::get('/my-requests', [BloodRequestController::class, 'myRequests']);
    Route::post('/requests/{bloodRequest}/cancel', [BloodRequestController::class, 'cancel']);

    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
});