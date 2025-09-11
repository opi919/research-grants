<?php

// routes/web.php
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Admin\UserManagementController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::get('/register', function () {
    return view('auth.register');
})->name('register');

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    
    Route::get('/pending', function () {
        return view('auth.pending');
    })->name('pending');
    
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('approved')
        ->name('dashboard');
    
    // Search routes - only for approved users
    Route::middleware(['approved', 'single.device'])->group(function () {
        Route::get('/search', [SearchController::class, 'index'])->name('search');
        Route::get('/search/export', [SearchController::class, 'export'])->name('search.export');
        Route::get('/grants/{grant}', [SearchController::class, 'show'])->name('search.show');
    });
    
    // Admin routes
    Route::middleware(['admin'])->prefix('admin')->name('admin.')->group(function () {
        Route::get('/users', [UserManagementController::class, 'index'])->name('users');
        Route::patch('/users/{user}/approve', [UserManagementController::class, 'approve'])->name('users.approve');
        Route::patch('/users/{user}/reject', [UserManagementController::class, 'reject'])->name('users.reject');
        Route::delete('/users/{user}', [UserManagementController::class, 'destroy'])->name('users.destroy');
        Route::post('/users/{user}/force-logout', [UserManagementController::class, 'forceLogout'])->name('users.force-logout');
    });
});