<?php

use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DonorController as AdminDonorController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\RequestController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AvailabilityController;
use App\Http\Controllers\BloodRequestController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DonorController;
use App\Http\Controllers\DonorSearchController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'home');

// Guests only: logged-in users get bounced to their own dashboard instead.
Route::middleware('guest')->group(function () {
    Route::get('/register', [RegisterController::class, 'show']);
    Route::post('/register', [RegisterController::class, 'store'])->middleware('throttle:5,1');
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'store'])->middleware('throttle:6,1');
});

// Any logged-in user, any role, can log out.
Route::post('/logout', [LoginController::class, 'destroy'])->middleware('auth');

// Admin landing page — real dashboard, admin only.
Route::get('/admin', [AdminDashboardController::class, 'index'])->middleware(['auth', 'admin']);

// User-only actions. 'not-admin' keeps an admin account out of donor/requester pages,
// even via direct URL, not just by hiding the sidebar link.
Route::middleware(['auth', 'not-admin'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/donor', [DonorController::class, 'edit']);
    Route::post('/donor', [DonorController::class, 'save']);
    Route::get('/requests/create', [BloodRequestController::class, 'create']);
    Route::post('/requests', [BloodRequestController::class, 'store']);
    Route::get('/my-requests', [BloodRequestController::class, 'myRequests']);
    Route::post('/requests/{bloodRequest}/cancel', [BloodRequestController::class, 'cancel']);
});

// Shared account pages — fine for any logged-in role to view.
Route::get('/profile', [ProfileController::class, 'edit'])->middleware('auth');
Route::put('/profile', [ProfileController::class, 'update'])->middleware('auth');

// Public donor search — guests can browse, only auth-gated actions differ inside the view.
Route::get('/donors', [DonorSearchController::class, 'index']);

Route::get('/availability', [AvailabilityController::class, 'index'])->middleware('auth');

// Admin-only area.
Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::get('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'edit']);
    Route::put('/profile', [\App\Http\Controllers\Admin\ProfileController::class, 'update']);

    Route::get('/inventory', [InventoryController::class, 'index']);
    Route::get('/inventory/create', [InventoryController::class, 'create']);
    Route::post('/inventory', [InventoryController::class, 'store']);
    Route::get('/inventory/{batch}/edit', [InventoryController::class, 'edit']);
    Route::put('/inventory/{batch}', [InventoryController::class, 'update']);
    Route::delete('/inventory/{batch}', [InventoryController::class, 'destroy']);

    Route::get('/donors', [AdminDonorController::class, 'index']);
    Route::get('/donors/{donor}/edit', [AdminDonorController::class, 'edit']);
    Route::put('/donors/{donor}', [AdminDonorController::class, 'update']);
    Route::delete('/donors/{donor}', [AdminDonorController::class, 'destroy']);

    Route::get('/requests', [RequestController::class, 'index']);
    Route::post('/requests/{bloodRequest}/status', [RequestController::class, 'updateStatus']);

    Route::get('/users', [UserController::class, 'index']);
    Route::get('/users/{user}/edit', [UserController::class, 'edit']);
    Route::put('/users/{user}', [UserController::class, 'update']);
    Route::delete('/users/{user}', [UserController::class, 'destroy']);

    Route::get('/reports', [ReportController::class, 'index']);
    Route::get('/reports/export/requests', [ReportController::class, 'exportRequests']);
});

// Notifications — any logged-in role (admin included) can view their own.
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllRead']);
});